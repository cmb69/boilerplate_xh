<?php

/**
 * Testing the CSRF protection.
 *
 * The environment variable CMSIMPLEDIR has to be set to the installation folder
 * (e.g. / or /cmsimple_xh/).
 *
 * PHP version 5
 *
 * @category  Testing
 * @package   Boilerplate
 * @author    The CMSimple_XH developers <devs@cmsimple-xh.org>
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013-2014 The CMSimple_XH developers <http://cmsimple-xh.org/?The_Team>
 * @copyright 2014-2021 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Boilerplate_XH
 */

namespace Boilerplate;

use PHPUnit_Framework_TestCase;

/**
 * A test case to actually check the CSRF protection.
 *
 * @category Testing
 * @package  Boilerplate
 * @author   The CMSimple_XH developers <devs@cmsimple-xh.org>
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Boilerplate_XH
 */
class CSRFAttackTest extends PHPUnit_Framework_TestCase
{
    /**
     * The URL of the CMSimple installation.
     *
     * @var string
     */
    private $url;

    /**
     * The cURL handle.
     *
     * @var resource
     */
    private $curlHandle;

    /**
     * The filename of the cookie file.
     *
     * @var string
     */
    private $cookieFile;

    /**
     * Sets up the test fixture.
     *
     * Logs in to back-end and stores cookies in a temp file.
     *
     * @return void
     */
    public function setUp()
    {
        $this->url = 'http://localhost' . getenv('CMSIMPLEDIR');
        $this->cookieFile = tempnam(sys_get_temp_dir(), 'CC');

        $this->curlHandle = curl_init($this->url . '?&login=true&keycut=test');
        curl_setopt($this->curlHandle, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_exec($this->curlHandle);
        curl_close($this->curlHandle);
    }

    /**
     * Sets the cURL options.
     *
     * @param array $fields A map of post fields.
     *
     * @return void
     */
    private function setCurlOptions($fields)
    {
        $options = array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIEFILE => $this->cookieFile
        );
        curl_setopt_array($this->curlHandle, $options);
    }

    /**
     * Tests an attack.
     *
     * @param array  $fields      A map of post fields.
     * @param string $queryString A query string.
     *
     * @return void
     *
     * @dataProvider dataForAttack
     */
    public function testAttack($fields, $queryString = null)
    {
        $url = $this->url . (isset($queryString) ? '?' . $queryString : '');
        $this->curlHandle = curl_init($url);
        $this->setCurlOptions($fields);
        curl_exec($this->curlHandle);
        $actual = curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);
        curl_close($this->curlHandle);
        $this->assertEquals(403, $actual);
    }

    /**
     * Provides data for testAttack().
     *
     * @return array
     */
    public function dataForAttack()
    {
        return [
            [
                [
                    'admin' => 'plugin_main',
                    'action' => 'new',
                    'boilerplate_name' => 'foo'
                ],
                'boilerplate'
            ],
            [
                [
                    'admin' => 'plugin_main',
                    'action' => 'save',
                    'boilerplate_name' => 'foo',
                    'boilerplate_text' => 'bar'
                ],
                'boilerplate'
            ],
            [
                [
                    'admin' => 'plugin_main',
                    'action' => 'delete',
                    'boilerplate_name' => 'foo'
                ],
                'boilerplate'
            ]
        ];
    }
}
