<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Wrapper for the PHPUnit XML configuration file.
 *
 * Example XML configuration file:
 * <code>
 * <?xml version="1.0" encoding="utf-8" ?>
 *
 * <phpunit backupGlobals="true"
 *          backupStaticAttributes="false"
 *          bootstrap="/path/to/bootstrap.php"
 *          cacheTokens="false"
 *          columns="80"
 *          colors="false"
 *          stderr="false"
 *          convertErrorsToExceptions="true"
 *          convertNoticesToExceptions="true"
 *          convertWarningsToExceptions="true"
 *          forceCoversAnnotation="false"
 *          mapTestClassNameToCoveredClassName="false"
 *          printerClass="PHPUnit_TextUI_ResultPrinter"
 *          processIsolation="false"
 *          stopOnError="false"
 *          stopOnFailure="false"
 *          stopOnIncomplete="false"
 *          stopOnRisky="false"
 *          stopOnSkipped="false"
 *          testSuiteLoaderClass="PHPUnit_Runner_StandardTestSuiteLoader"
 *          timeoutForSmallTests="1"
 *          timeoutForMediumTests="10"
 *          timeoutForLargeTests="60"
 *          beStrictAboutTestsThatDoNotTestAnything="false"
 *          beStrictAboutOutputDuringTests="false"
 *          beStrictAboutTestSize="false"
 *          beStrictAboutTodoAnnotatedTests="false"
 *          checkForUnintentionallyCoveredCode="false"
 *          disallowChangesToGlobalState="false"
 *          verbose="false">
 *   <testsuites>
 *     <testsuite name="My Test Suite">
 *       <directory suffix="Test.php" phpVersion="5.3.0" phpVersionOperator=">=">/path/to/files</directory>
 *       <file phpVersion="5.3.0" phpVersionOperator=">=">/path/to/MyTest.php</file>
 *       <exclude>/path/to/files/exclude</exclude>
 *     </testsuite>
 *   </testsuites>
 *
 *   <groups>
 *     <include>
 *       <group>name</group>
 *     </include>
 *     <exclude>
 *       <group>name</group>
 *     </exclude>
 *   </groups>
 *
 *   <filter>
 *     <blacklist>
 *       <directory suffix=".php">/path/to/files</directory>
 *       <file>/path/to/file</file>
 *       <exclude>
 *         <directory suffix=".php">/path/to/files</directory>
 *         <file>/path/to/file</file>
 *       </exclude>
 *     </blacklist>
 *     <whitelist addUncoveredFilesFromWhitelist="true"
 *                processUncoveredFilesFromWhitelist="false">
 *       <directory suffix=".php">/path/to/files</directory>
 *       <file>/path/to/file</file>
 *       <exclude>
 *         <directory suffix=".php">/path/to/files</directory>
 *         <file>/path/to/file</file>
 *       </exclude>
 *     </whitelist>
 *   </filter>
 *
 *   <listeners>
 *     <listener class="MyListener" file="/optional/path/to/MyListener.php">
 *       <arguments>
 *         <array>
 *           <element key="0">
 *             <string>Sebastian</string>
 *           </element>
 *         </array>
 *         <integer>22</integer>
 *         <string>April</string>
 *         <double>19.78</double>
 *         <null/>
 *         <object class="stdClass"/>
 *         <file>MyRelativeFile.php</file>
 *         <directory>MyRelativeDir</directory>
 *       </arguments>
 *     </listener>
 *   </listeners>
 *
 *   <logging>
 *     <log type="coverage-html" target="/tmp/report" lowUpperBound="50" highLowerBound="90"/>
 *     <log type="coverage-clover" target="/tmp/clover.xml"/>
 *     <log type="coverage-crap4j" target="/tmp/crap.xml" threshold="30"/>
 *     <log type="json" target="/tmp/logfile.json"/>
 *     <log type="plain" target="/tmp/logfile.txt"/>
 *     <log type="tap" target="/tmp/logfile.tap"/>
 *     <log type="junit" target="/tmp/logfile.xml" logIncompleteSkipped="false"/>
 *     <log type="testdox-html" target="/tmp/testdox.html"/>
 *     <log type="testdox-text" target="/tmp/testdox.txt"/>
 *   </logging>
 *
 *   <php>
 *     <includePath>.</includePath>
 *     <ini name="foo" value="bar"/>
 *     <const name="foo" value="bar"/>
 *     <var name="foo" value="bar"/>
 *     <env name="foo" value="bar"/>
 *     <post name="foo" value="bar"/>
 *     <get name="foo" value="bar"/>
 *     <cookie name="foo" value="bar"/>
 *     <server name="foo" value="bar"/>
 *     <files name="foo" value="bar"/>
 *     <request name="foo" value="bar"/>
 *   </php>
 *
 *   <selenium>
 *     <browser name="Firefox on Linux"
 *              browser="*firefox /usr/lib/firefox/firefox-bin"
 *              host="my.linux.box"
 *              port="4444"
 *              timeout="30000"/>
 *   </selenium>
 * </phpunit>
 * </code>
 *
 * @since Class available since Release 3.2.0
 */
class PHPUnit_Util_Configuration
{
    private static $instances = array();

    protected $document;
    protected $xpath;
    protected $filename;

    /**
     * Loads a PHPUnit configuration file.
     *
     * @param string $filename
     */
    protected function __construct($filename)
    {
        $this->filename = $filename;
        $this->document = PHPUnit_Util_XML::loadFile($filename, false, true, true);
        $this->xpath    = new DOMXPath($this->document);
    }

    /**
     * @since  Method available since Release 3.4.0
     */
    final private function __clone()
    {
    }

    /**
     * Returns a PHPUnit configuration object.
     *
     * @param string $filename
     *
     * @return PHPUnit_Util_Configuration
     *
     * @since  Method available since Release 3.4.0
     */
    public static function getInstance($filename)
    {
        $realpath = realpath($filename);

        if ($realpath === false) {
            throw new PHPUnit_Framework_Exception(
                sprintf(
                    'Could not read "%s".',
                    $filename
                )
            );
        }

        if (!isset(self::$instances[$realpath])) {
            self::$instances[$realpath] = new self($realpath);
        }

        return self::$instances[$realpath];
    }

    /**
     * Returns the realpath to the configuration file.
     *
     * @return string
     *
     * @since  Method available since Release 3.6.0
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Returns the configuration for SUT filtering.
     *
     * @return array
     *
     * @since  Method available since Release 3.2.1
     */
    public function getFilterConfiguration()
    {
        $addUncoveredFilesFromWhitelist     = true;
        $processUncoveredFilesFromWhitelist = false;

        $tmp = $this->xpath->query('filter/whitelist');

        if ($tmp->length == 1) {
            if ($tmp->item(0)->hasAttribute('addUncoveredFilesFromWhitelist')) {
                $addUncoveredFilesFromWhitelist = $this->getBoolean(
                    (string) $tmp->item(0)->getAttribute(
                        'addUncoveredFilesFromWhitelist'
                    ),
                    true
                );
            }

            if ($tmp->item(0)->hasAttribute('processUncoveredFilesFromWhitelist')) {
                $processUncoveredFilesFromWhitelist = $this->getBoolean(
                    (string) $tmp->item(0)->getAttribute(
                        'processUncoveredFilesFromWhitelist'
                    ),
                    false
                );
            }
        }

        return array(
          'blacklist' => array(
            'include' => array(
              'directory' => $this->readFilterDirectories(
                  'filter/blacklist/directory'
              ),
              'file' => $this->readFilterFiles(
                  'filter/blacklist/file'
              )
            ),
            'exclude' => array(
              'directory' => $this->readFilterDirectories(
                  'filter/blacklist/exclude/directory'
              ),
              'file' => $this->readFilterFiles(
                  'filter/blacklist/exclude/file'
              )
            )
          ),
          'whitelist' => array(
            'addUncoveredFilesFromWhitelist'     => $addUncoveredFilesFromWhitelist,
            'processUncoveredFilesFromWhitelist' => $processUncoveredFilesFromWhitelist,
            'include'                            => array(
              'directory' => $this->readFilterDirectories(
                  'filter/whitelist/directory'
              ),
              'file' => $this->readFilterFiles(
                  'filter/whitelist/file'
              )
            ),
            'exclude' => array(
              'directory' => $this->readFilterDirectories(
                  'filter/whitelist/exclude/directory'
              ),
              'file' => $this->readFilterFiles(
                  'filter/whitelist/exclude/file'
              )
            )
          )
        );
    }

    /**
     * Returns the configuration for groups.
     *
     * @return array
     *
     * @since  Method available since Release 3.2.1
     */
    public function getGroupConfiguration()
    {
        $groups = array(
          'include' => array(),
          'exclude' => array()
        );

        foreach ($this->xpath->query('groups/include/group') as $group) {
            $groups['include'][] = (string) $group->textContent;
        }

        foreach ($this->xpath->query('groups/exclude/group') as $group) {
            $groups['exclude'][] = (string) $group->textContent;
        }

        return $groups;
    }

    /**
     * Returns the configuration for listeners.
     *
     * @return array
     *
     * @since  Method available since Release 3.4.0
     */
    public function getListenerConfiguration()
    {
        $result = array();

        foreach ($this->xpath->query('listeners/listener') as $listener) {
            $class     = (string) $listener->getAttribute('class');
            $file      = '';
            $arguments = array();

            if ($listener->getAttribute('file')) {
                $file = $this->toAbsolutePath(
                    (string) $listener->getAttribute('file'),
                    true
                );
            }

            foreach ($listener->childNodes as $node) {
                if ($node instanceof DOMElement && $node->tagName == 'arguments') {
                    foreach ($node->childNodes as $argument) {
                        if ($argument instanceof DOMElement) {
                            if ($argument->tagName == 'file' ||
                            $argument->tagName == 'directory') {
                                $arguments[] = $this->toAbsolutePath((string) $argument->textContent);
                            } else {
                                $arguments[] = PHPUnit_Util_XML::xmlToVariable($argument);
                            }
                        }
                    }
                }
            }

            $result[] = array(
              'class'     => $class,
              'file'      => $file,
              'arguments' => $arguments
            );
        }

        return $result;
    }

    /**
     * Returns the logging configuration.
     *
     * @return array
     */
    public function getLoggingConfiguration()
    {
        $result = array();

        foreach ($this->xpath->query('logging/log') as $log) {
            $type   = (string) $log->getAttribute('type');
            $target = (string) $log->getAttribute('target');

            if (!$target) {
                continue;
            }

            $target = $this->toAbsolutePath($target);

            if ($type == 'coverage-html') {
                if ($log->hasAttribute('lowUpperBound')) {
                    $result['lowUpperBound'] = $this->getInteger(
                        (string) $log->getAttribute('lowUpperBound'),
                        50
                    );
                }

                if ($log->hasAttribute('highLowerBound')) {
                    $result['highLowerBound'] = $this->getInteger(
                        (string) $log->getAttribute('highLowerBound'),
                        90
                    );
                }
            } elseif ($type == 'coverage-crap4j') {
                if ($log->hasAttribute('threshold')) {
                    $result['crap4jThreshold'] = $this->getInteger(
                        (string) $log->getAttribute('threshold'),
                        30
                    );
                }
            } elseif ($type == 'junit') {
                if ($log->hasAttribute('logIncompleteSkipped')) {
                    $result['logIncompleteSkipped'] = $this->getBoolean(
                        (string) $log->getAttribute('logIncompleteSkipped'),
                        false
                    );
                }
            } elseif ($type == 'coverage-text') {
                if ($log->hasAttribute('showUncoveredFiles')) {
                    $result['coverageTextShowUncoveredFiles'] = $this->getBoolean(
                        (string) $log->getAttribute('showUncoveredFiles'),
                        false
                    );
                }
                if ($log->hasAttribute('showOnlySummary')) {
                    $result['coverageTextShowOnlySummary'] = $this->getBoolean(
                        (string) $log->getAttribute('showOnlySummary'),
                        false
                    );
                }
            }

            $result[$type] = $target;
        }

        return $result;
    }

    /**
     * Returns the PHP configuration.
     *
     * @return array
     *
     * @since  Method available since Release 3.2.1
     */
    public function getPHPConfiguration()
    {
        $result = array(
          'include_path' => array(),
          'ini'          => array(),
          'con