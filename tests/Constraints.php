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
     * Evaluates the constraint for parameter $file. Returns TRUE if the
     * constraint is met, FALSE otherwise.
     *
     * @param string $file Value or object to evaluate.
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
     * Evaluates the constraint for parameter $fileName. Returns TRUE if the
     * constraint is met, FALSE otherwise.
     *
     * @param string $fileName Value or object to evaluate.
     * @return bool
     */
    public function evaluate($fileName)
    {
        $lint = $this->lintFile($fileName);
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

/**
 * ConstraintXmlStringValidatesDtdUri
 *
 * For asserting that XML validates a DTD.
 *
 * To test that an XML string validates a DTD URI.
 *
 * Example:
 *
 *   $this->assertXmlStringValidatesDtdUri($xml, $dtd);
 *
 * PHP does not do the resolvement of all dtd URIs, especially if
 * those are files in the local system. To make those DTDs useable,
 * the PHP data:// stream wrapper can be used. The following
 * example shows how to do this:
 *
 * Example:
 *
 *   $dtdFile = 'path/to/local/file.dtd';
 * 	 $dtdText = file_get_contents($dtdFile);
 *   $dtd = 'data://text/plain;base64,'.base64_encode($dtdText);
 *   $this->assertXmlStringValidatesDtdUri($xml, $dtd);
 *
 */
class ConstraintXmlStringValidatesDtdUri extends \PHPUnit_Framework_Constraint
{
    /**
     * @var string XML
     */
    private $xml;

    /**
     * @var string DTD URI
     */
    private $dtd;

    /**
     * @var array local store for validation errors.
     */
    private $errors;

    /**
     * @param string $xml
     */
    public function __construct($xml)
    {
        $this->xml = $xml;
    }

    public function validateErrorHandler ($no, $message, $file = null, $line = null, $context = null) {
        $nice = $message;
        $prefix = 'DOMDocument::validate(): ';
        if ($prefix === substr($nice,0, strlen($prefix))) {
            $nice = substr($nice, strlen($prefix));
        }

        $this->errors[] = $nice;
    }

    /**
     * validateDTD
     *
     * @param string $xml XML
     * @param string $dtd DTD URI
     * @return bool
     */
    private function validateDTD($xml, $dtd)
    {
        $importDoc = new \DOMDocument();
        $importDoc->loadXML($xml);

        $rootNode = $importDoc->documentElement;
        $rootName = $rootNode->tagName;
        $version = $importDoc->xmlVersion;
        $encoding = $importDoc->encoding;

        $assertImplementation = new \DOMImplementation;
        $assertDocType = $assertImplementation->createDocumentType($rootName, '', $dtd);
        $assertDoc = $assertImplementation->createDocument('', '', $assertDocType);
        $assertDoc->xmlVersion = $version;
        $assertDoc->encoding = $encoding;
        $importNode = $assertDoc->importNode($rootNode, true);
        $assertDoc->appendChild($importNode);

        $this->errors = null;
        set_error_handler(array($this, "validateErrorHandler"));
        $result = $assertDoc->validate();
        restore_error_handler();
        return $result;
    }

    /**
     * Evaluates the constraint for parameter $dtd. Returns TRUE if the
     * constraint is met, FALSE otherwise.
     *
     * @param mixed $other Value or object to evaluate.
     * @return bool
     */
    public function evaluate($dtd)
    {
        return $this->validateDTD($this->xml, $dtd);
    }

    /**
     * @param string  $dtd
     * @param string  $description
     * @param boolean $not
     */
    protected function customFailureDescription($dtd, $description, $not)
    {
        $dtdLabel = $dtd;
        if (strlen($dtdLabel)>40) {
            $dtdLabel = substr($dtdlabel, 0, 37). '...';
        }
        return sprintf('Assertion that XML %s DTD "%s" has failed (%d errors).', $not ? 'does not validate' : 'validates', $dtdLabel, count($this->errors));
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return 'validates DTD';
    }
}