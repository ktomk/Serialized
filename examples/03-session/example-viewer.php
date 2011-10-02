<?php
/**
 * Serialized - PHP Library for Serialized Data
 *
 * Copyright (C) 2010-2011 Tom Klingenberg, some rights reserved
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program in a file called COPYING. If not, see
 * <http://www.gnu.org/licenses/> and please report back to the original
 * author.
 *
 * @author Tom Klingenberg <http://lastflood.com/>
 * @version 0.2.6
 * @package Examples
 */

# Session viewer sample application (web)

Use \Serialized\SessionParser;

require_once(__DIR__.'/../../src/Serialized.php');

$router = new Router(new Request());

echo $router->getResponse();

function action_delete($name)
{
	$session = new Session($name);
	$file = $session->getFile();
	unlink($file);
}

function action_error(Exception $e)
{
?>
<h2>Error: <?php echo $e->getMessage(); ?></h2>
<?php
action_list(); // backup listing for base navigation
}

function action_list()
{
	$viewLink = '?action=view&name=%s';
	$deleteLink = '?action=delete&name=%s';
	$sessions = new Sessions();
	$list = $sessions->getList();
	$count = count($list);
?>
<table border="1" id="list">
<tr>
	<th colspan="3">Sessions (<?php echo $count; ?>)</th>
</tr>
<tr>
	<th>Name</th>
	<th>Date/Time</th>
	<th>Actions</th>
</tr>
<?php
	foreach($list as $name => $session)
	{
		$filename = $session['file'];
		$date = date('Y-m-d H:i:s', filemtime($filename));
		$linkView = sprintf($viewLink, urlencode($name));
		$linkDelete = sprintf($deleteLink, urlencode($name));
?>
<tr>
	<td><a href="<?php echo $linkView; ?>"><?php echo $name; ?></a></td>
	<td><?php echo $date; ?></td>
	<td><a href="<?php echo $linkDelete; ?>">[delete]</a></td>
</tr>
<?php } ?>
</table>
<?php
}

function action_view($name)
{
	$session = new Session($name);
	$file = $session->getFile();
	$serializedSession = file_get_contents($file);
	$parser = new SessionParser($serializedSession);
?>
<h2><?php echo $name; ?></h2>
<div><?php echo htmlspecialchars($file); ?></div>
<pre style="height:380px; width:760px; overflow:auto; border:1px solid #ccc;">
<?php echo htmlspecialchars($parser->dump()); ?>
</pre>
<?php
}

class Router
{
	/**
	 * @var Request
	 */
	private $request;
	public function __construct(Request $request)
	{
		$this->request = $request;
	}
	
	public function getResponse()
	{
		$request = $this->request;

		$action = $request->getParameter('action', 'list');
		
		try
		{
			return $this->route($action);
		} catch(Exception $e) {
			action_error($e);
		}
	}
	
	public function Route($action)
	{
		switch($action)
		{
			case 'delete':
				action_delete($this->request->getParameter('name'));
				action_list();
				break;
				
			case 'view':
				action_view($this->request->getParameter('name'));
				action_list();
				break;
			
			case 'list':
				action_list();
				break;

			default:
				throw new InvalidArgumentException(sprintf('Invalid Action (%s).', $action));
		}
	}
}

class Request
{
	public function getParameter($name, $default = NULL)
	{
		return isset($_GET[$name]) ? $_GET[$name] : $default;
	}
}

class Session
{
	private $name;
	private $file;
	
	public function __construct($name)
	{
		$sessions = new Sessions();
		$list = $sessions->getList();
		if (!isset($list[$name]))
			throw new InvalidArgumentException(sprintf('Invalid session name ("%s").', $name));
		$this->name = $name;
		$this->file = $list[$name]['file'];
	}
	public function getFile()
	{
		return $this->file;
	}
}

class Sessions
{
	public function getConfiguration()
	{
		$config = array();

		foreach(array('serialize_handler', 'save_path', 'save_handler', ) as $setting)
		{
			$config[$setting] = ini_get('session.'.$setting);
		}
		return $config;
	}
	
	public function getList()
	{
		$config = $this->getConfiguration();
		$path = $config['save_path'];
		$filePattern = 'sess_*';
		$namePattern = 'sess_%s';

		$files = glob($path.'/'.$filePattern);
		if (false === $files) throw new Exception('Failed to glob session files.');

		$sessions = array();
		foreach($files as $file)
		{
			sscanf(basename($file), $namePattern, $name);
			
			$sessions[$name] = array('file' => $file);
		}
		return $sessions;
	}
}
