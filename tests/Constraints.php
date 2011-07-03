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
 * @version 0.2.5
 * @package Tests
 */

Namespace Serialized;

/**
 * ConstraintLastError
 *
 * For asserting the last error message in a file.
 *
 * To test trigger_error().
 *
 * Example:
 *
 *   $this->assertLastError('Error Message', 'basenameOfFile.php');
 *
 */
class ConstraintLastError extends \PHPUnit_Framework_Constraint {
	private $file;
	private $error;
	public function __construct($error) {
		$this->error = $error;
	}
    /**
     * Evaluates the constraint for parameter $other. Returns TRUE if the
     * constraint is met, FALSE otherwise.
     *
     * @param mixed $other Value or object to evaluate.
     * @return bool
     */
	public function evaluate($file)
	{
		$this->file = $file;
		$error = $this->error;
		$lastError = error_get_last();
		if (NULL === $lastError)
			return false;

		$last_message = $lastError['message'];
		$last_file = $lastError['file'];

		return ($error == $last_message && basename($file) == basename($last_file));
	}

    /**
     * @param mixed   $other
     * @param string  $description
     * @param boolean $not
     */
    protected function customFailureDescription($other, $description, $not)
    {
		return sprintf('Failed asserting that the last error %s', basename($other), $not ? '' : 'no ', implode("\n  - ", $this->lines));
    }


    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return sprintf('was %s in file %s.', $this->error, $this->file);
    }
}

/**
 * ConstraintLint
 *
 * For asserting that a file passes php lint (php -l).
 *
 * To test php's internal lint syntax check on a php file. Useful to run
 * tests on files that will be automatically included but preventing fatal
 * errors in tests because included files would not lint.
 *
 * Example:
 *
 *   $this->assertLint($fileName); // test fails if file does not lint
 *   require $fileName;
 *
 */
class ConstraintLint extends \PHPUnit_Framework_Constraint {
	private $lines;
    private function lintFile($fileName) {
		$return = 0;
		$lintOutput = array();
		$exitStatus = 0;
		exec("php -l " . escapeshellarg($fileName), $lintOutput, $return);
		if ($return != 0) {
			$exitStatus = 1;
			array_splice($lintOutput, -2);
		}
		$this->lines = $lintOutput;
		return $exitStatus;
	}
	/**
     * Evaluates the constraint for parameter $other. Returns TRUE if the
     * constraint is met, FALSE otherwise.
     *
     * @param mixed $other Value or object to evaluate.
     * @return bool
     */
    public function evaluate($other)
    {
		$lint = $this->lintFile($other);
		return $lint != 1;
    }

    /**
     * @param mixed   $other
     * @param string  $description
     * @param boolean $not
     */
    protected function customFailureDescription($other, $description, $not)
    {
		return sprintf('Assertion that the file %s has %slints failed: %s', basename($other), $not ? '' : 'no ', implode("\n  - ", $this->lines));
    }


    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return 'can lint';
    }
}

// @todo XMLDTD Constraint
