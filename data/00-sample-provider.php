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
 * @version 0.1.2
 * @package Tests
 */

/**
 * Just return an array full of serialized values.
 *
 * Add your own files with a similar naming into the same directory and
 * they will become automatically part of the \Serialized\DataTest.
 */
return array(
  "N;",
  "O:8:\"stdClass\":2:{s:6:\"normal\";r:1;s:9:\"reference\";R:1;}",
  "a:1:{i:0;s:47:\"yellowsunshineshinesyellowoverthebridgeandthen?\";}",
  "a:1:{s:14:\"removedbwidget\";b:1;}",
  "a:77:{s:47:\"category/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?\$\";s:52:\"index.php?category_name=\$matches[1]&feed=\$matches[2]\";s:42:\"category/(.+?)/(feed|rdf|rss|rss2|atom)/?\$\";s:52:\"index.php?category_name=\$matches[1]&feed=\$matches[2]\";s:35:\"category/(.+?)/page/?([0-9]{1,})/?\$\";s:53:\"index.php?category_name=\$matches[1]&paged=\$matches[2]\";s:17:\"category/(.+?)/?\$\";s:35:\"index.php?category_name=\$matches[1]\";s:44:\"tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?\$\";s:42:\"index.php?tag=\$matches[1]&feed=\$matches[2]\";s:39:\"tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?\$\";s:42:\"index.php?tag=\$matches[1]&feed=\$matches[2]\";s:32:\"tag/([^/]+)/page/?([0-9]{1,})/?\$\";s:43:\"index.php?tag=\$matches[1]&paged=\$matches[2]\";s:14:\"tag/([^/]+)/?\$\";s:25:\"index.php?tag=\$matches[1]\";s:45:\"type/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?\$\";s:50:\"index.php?post_format=\$matches[1]&feed=\$matches[2]\";s:40:\"type/([^/]+)/(feed|rdf|rss|rss2|atom)/?\$\";s:50:\"index.php?post_format=\$matches[1]&feed=\$matches[2]\";s:33:\"type/([^/]+)/page/?([0-9]{1,})/?\$\";s:51:\"index.php?post_format=\$matches[1]&paged=\$matches[2]\";s:15:\"type/([^/]+)/?\$\";s:33:\"index.php?post_format=\$matches[1]\";s:47:\"person/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?\$\";s:45:\"index.php?people=\$matches[1]&feed=\$matches[2]\";s:42:\"person/([^/]+)/(feed|rdf|rss|rss2|atom)/?\$\";s:45:\"index.php?people=\$matches[1]&feed=\$matches[2]\";s:35:\"person/([^/]+)/page/?([0-9]{1,})/?\$\";s:46:\"index.php?people=\$matches[1]&paged=\$matches[2]\";s:17:\"person/([^/]+)/?\$\";s:28:\"index.php?people=\$matches[1]\";s:14:\".*wp-atom.php\$\";s:19:\"index.php?feed=atom\";s:13:\".*wp-rdf.php\$\";s:18:\"index.php?feed=rdf\";s:13:\".*wp-rss.php\$\";s:18:\"index.php?feed=rss\";s:14:\".*wp-rss2.php\$\";s:19:\"index.php?feed=rss2\";s:14:\".*wp-feed.php\$\";s:19:\"index.php?feed=feed\";s:22:\".*wp-commentsrss2.php\$\";s:34:\"index.php?feed=rss2&withcomments=1\";s:32:\"feed/(feed|rdf|rss|rss2|atom)/?\$\";s:27:\"index.php?&feed=\$matches[1]\";s:27:\"(feed|rdf|rss|rss2|atom)/?\$\";s:27:\"index.php?&feed=\$matches[1]\";s:20:\"page/?([0-9]{1,})/?\$\";s:28:\"index.php?&paged=\$matches[1]\";s:41:\"comments/feed/(feed|rdf|rss|rss2|atom)/?\$\";s:42:\"index.php?&feed=\$matches[1]&withcomments=1\";s:36:\"comments/(feed|rdf|rss|rss2|atom)/?\$\";s:42:\"index.php?&feed=\$matches[1]&withcomments=1\";s:29:\"comments/page/?([0-9]{1,})/?\$\";s:28:\"index.php?&paged=\$matches[1]\";s:44:\"search/(.+)/feed/(feed|rdf|rss|rss2|atom)/?\$\";s:40:\"index.php?s=\$matches[1]&feed=\$matches[2]\";s:39:\"search/(.+)/(feed|rdf|rss|rss2|atom)/?\$\";s:40:\"index.php?s=\$matches[1]&feed=\$matches[2]\";s:32:\"search/(.+)/page/?([0-9]{1,})/?\$\";s:41:\"index.php?s=\$matches[1]&paged=\$matches[2]\";s:14:\"search/(.+)/?\$\";s:23:\"index.php?s=\$matches[1]\";s:47:\"author/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?\$\";s:50:\"index.php?author_name=\$matches[1]&feed=\$matches[2]\";s:42:\"author/([^/]+)/(feed|rdf|rss|rss2|atom)/?\$\";s:50:\"index.php?author_name=\$matches[1]&feed=\$matches[2]\";s:35:\"author/([^/]+)/page/?([0-9]{1,})/?\$\";s:51:\"index.php?author_name=\$matches[1]&paged=\$matches[2]\";s:17:\"author/([^/]+)/?\$\";s:33:\"index.php?author_name=\$matches[1]\";s:69:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?\$\";s:80:\"index.php?year=\$matches[1]&monthnum=\$matches[2]&day=\$matches[3]&feed=\$matches[4]\";s:64:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?\$\";s:80:\"index.php?year=\$matches[1]&monthnum=\$matches[2]&day=\$matches[3]&feed=\$matches[4]\";s:57:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?\$\";s:81:\"index.php?year=\$matches[1]&monthnum=\$matches[2]&day=\$matches[3]&paged=\$matches[4]\";s:39:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?\$\";s:63:\"index.php?year=\$matches[1]&monthnum=\$matches[2]&day=\$matches[3]\";s:56:\"([0-9]{4})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?\$\";s:64:\"index.php?year=\$matches[1]&monthnum=\$matches[2]&feed=\$matches[3]\";s:51:\"([0-9]{4})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?\$\";s:64:\"index.php?year=\$matches[1]&monthnum=\$matches[2]&feed=\$matches[3]\";s:44:\"([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?\$\";s:65:\"index.php?year=\$matches[1]&monthnum=\$matches[2]&paged=\$matches[3]\";s:26:\"([0-9]{4})/([0-9]{1,2})/?\$\";s:47:\"index.php?year=\$matches[1]&monthnum=\$matches[2]\";s:43:\"([0-9]{4})/feed/(feed|rdf|rss|rss2|atom)/?\$\";s:43:\"index.php?year=\$matches[1]&feed=\$matches[2]\";s:38:\"([0-9]{4})/(feed|rdf|rss|rss2|atom)/?\$\";s:43:\"index.php?year=\$matches[1]&feed=\$matches[2]\";s:31:\"([0-9]{4})/page/?([0-9]{1,})/?\$\";s:44:\"index.php?year=\$matches[1]&paged=\$matches[2]\";s:13:\"([0-9]{4})/?\$\";s:26:\"index.php?year=\$matches[1]\";s:47:\"[0-9]{4}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/?\$\";s:32:\"index.php?attachment=\$matches[1]\";s:57:\"[0-9]{4}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/trackback/?\$\";s:37:\"index.php?attachment=\$matches[1]&tb=1\";s:77:\"[0-9]{4}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?\$\";s:49:\"index.php?attachment=\$matches[1]&feed=\$matches[2]\";s:72:\"[0-9]{4}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?\$\";s:49:\"index.php?attachment=\$matches[1]&feed=\$matches[2]\";s:72:\"[0-9]{4}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?\$\";s:50:\"index.php?attachment=\$matches[1]&cpage=\$matches[2]\";s:44:\"([0-9]{4})/([0-9]{1,2})/([^/]+)/trackback/?\$\";s:69:\"index.php?year=\$matches[1]&monthnum=\$matches[2]&name=\$matches[3]&tb=1\";s:64:\"([0-9]{4})/([0-9]{1,2})/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?\$\";s:81:\"index.php?year=\$matches[1]&monthnum=\$matches[2]&name=\$matches[3]&feed=\$matches[4]\";s:59:\"([0-9]{4})/([0-9]{1,2})/([^/]+)/(feed|rdf|rss|rss2|atom)/?\$\";s:81:\"index.php?year=\$matches[1]&monthnum=\$matches[2]&name=\$matches[3]&feed=\$matches[4]\";s:52:\"([0-9]{4})/([0-9]{1,2})/([^/]+)/page/?([0-9]{1,})/?\$\";s:82:\"index.php?year=\$matches[1]&monthnum=\$matches[2]&name=\$matches[3]&paged=\$matches[4]\";s:59:\"([0-9]{4})/([0-9]{1,2})/([^/]+)/comment-page-([0-9]{1,})/?\$\";s:82:\"index.php?year=\$matches[1]&monthnum=\$matches[2]&name=\$matches[3]&cpage=\$matches[4]\";s:44:\"([0-9]{4})/([0-9]{1,2})/([^/]+)(/[0-9]+)?/?\$\";s:81:\"index.php?year=\$matches[1]&monthnum=\$matches[2]&name=\$matches[3]&page=\$matches[4]\";s:36:\"[0-9]{4}/[0-9]{1,2}/[^/]+/([^/]+)/?\$\";s:32:\"index.php?attachment=\$matches[1]\";s:46:\"[0-9]{4}/[0-9]{1,2}/[^/]+/([^/]+)/trackback/?\$\";s:37:\"index.php?attachment=\$matches[1]&tb=1\";s:66:\"[0-9]{4}/[0-9]{1,2}/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?\$\";s:49:\"index.php?attachment=\$matches[1]&feed=\$matches[2]\";s:61:\"[0-9]{4}/[0-9]{1,2}/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?\$\";s:49:\"index.php?attachment=\$matches[1]&feed=\$matches[2]\";s:61:\"[0-9]{4}/[0-9]{1,2}/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?\$\";s:50:\"index.php?attachment=\$matches[1]&cpage=\$matches[2]\";s:51:\"([0-9]{4})/([0-9]{1,2})/comment-page-([0-9]{1,})/?\$\";s:65:\"index.php?year=\$matches[1]&monthnum=\$matches[2]&cpage=\$matches[3]\";s:38:\"([0-9]{4})/comment-page-([0-9]{1,})/?\$\";s:44:\"index.php?year=\$matches[1]&cpage=\$matches[2]\";s:25:\".+?/attachment/([^/]+)/?\$\";s:32:\"index.php?attachment=\$matches[1]\";s:35:\".+?/attachment/([^/]+)/trackback/?\$\";s:37:\"index.php?attachment=\$matches[1]&tb=1\";s:55:\".+?/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?\$\";s:49:\"index.php?attachment=\$matches[1]&feed=\$matches[2]\";s:50:\".+?/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?\$\";s:49:\"index.php?attachment=\$matches[1]&feed=\$matches[2]\";s:50:\".+?/attachment/([^/]+)/comment-page-([0-9]{1,})/?\$\";s:50:\"index.php?attachment=\$matches[1]&cpage=\$matches[2]\";s:18:\"(.+?)/trackback/?\$\";s:35:\"index.php?pagename=\$matches[1]&tb=1\";s:38:\"(.+?)/feed/(feed|rdf|rss|rss2|atom)/?\$\";s:47:\"index.php?pagename=\$matches[1]&feed=\$matches[2]\";s:33:\"(.+?)/(feed|rdf|rss|rss2|atom)/?\$\";s:47:\"index.php?pagename=\$matches[1]&feed=\$matches[2]\";s:26:\"(.+?)/page/?([0-9]{1,})/?\$\";s:48:\"index.php?pagename=\$matches[1]&paged=\$matches[2]\";s:33:\"(.+?)/comment-page-([0-9]{1,})/?\$\";s:48:\"index.php?pagename=\$matches[1]&cpage=\$matches[2]\";s:18:\"(.+?)(/[0-9]+)?/?\$\";s:47:\"index.php?pagename=\$matches[1]&page=\$matches[2]\";}",
);