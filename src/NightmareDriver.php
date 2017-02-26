<?php

namespace Behat\Mink\Driver;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use DnodeSyncClient\Connection;
use Symfony\Component\Process\Process;
use DnodeSyncClient\Dnode;

class NightmareDriver extends CoreDriver
{
    /**
     * @var Process
     */
    protected $nodeProcess;

    /**
     * @var int
     */
    protected $nodePort = 6666;

    /**
     * @var Connection
     */
    protected $nodeClient;

    /**
     * @inheritdoc
     */
    public function start()
    {
        try {
            // TODO add more config options (eg; node path, env vars, args, etc)
            $this->nodeProcess = new Process($this->buildServerCmd(), dirname(__DIR__));
            $this->nodeProcess->setTimeout(null);
            $this->nodeProcess->start(/*function($type, $output) {
                echo $output;
            }*/);
            $this->nodeClient = (new Dnode())->connect('127.0.0.1', $this->nodePort);
        } catch (\Exception $ex) {
            throw new DriverException('Error while starting: ' . $ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * @inheritdoc
     */
    public function isStarted()
    {
        return $this->nodeProcess->isStarted();
    }

    /**
     * @inheritdoc
     */
    public function stop()
    {
        try {
            $this->nodeClient->close();
            $this->nodeProcess->stop();
        } catch (\Exception $ex) {
            throw new DriverException('Error while stopping: ' . $ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * @inheritdoc
     */
    public function reset()
    {
        $this->stop();
        $this->start();
    }

    /**
     * @inheritdoc
     */
    public function visit($url)
    {
        $this->sendAndWaitWithoutResult('visit', [$url]);
    }

    /**
     * @inheritdoc
     */
    public function getCurrentUrl()
    {
        return $this->sendAndWaitWithResult('getCurrentUrl');
    }

    /**
     * @inheritdoc
     */
    public function reload()
    {
        $this->sendAndWaitWithoutResult('reload');
    }

    /**
     * @inheritdoc
     */
    public function forward()
    {
        $this->sendAndWaitWithoutResult('forward');
    }

    /**
     * @inheritdoc
     */
    public function back()
    {
        $this->sendAndWaitWithoutResult('back');
    }

    /**
     * Sets HTTP Basic authentication parameters.
     *
     * @param string|Boolean $user user name or false to disable authentication
     * @param string $password password
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function setBasicAuth($user, $password)
    {
        // TODO: Implement setBasicAuth() method.
    }

    /**
     * Switches to specific browser window.
     *
     * @param string $name window name (null for switching back to main window)
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function switchToWindow($name = null)
    {
        // TODO: Implement switchToWindow() method.
    }

    /**
     * Switches to specific iFrame.
     *
     * @param string $name iframe name (null for switching back)
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function switchToIFrame($name = null)
    {
        // TODO: Implement switchToIFrame() method.
    }

    /**
     * Sets specific request header on client.
     *
     * @param string $name
     * @param string $value
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function setRequestHeader($name, $value)
    {
        // TODO: Implement setRequestHeader() method.
    }

    /**
     * Returns last response headers.
     *
     * @return array
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getResponseHeaders()
    {
        // TODO: Implement getResponseHeaders() method.
    }

    /**
     * Sets cookie.
     *
     * @param string $name
     * @param string $value
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function setCookie($name, $value = null)
    {
        // TODO: Implement setCookie() method.
    }

    /**
     * Returns cookie by name.
     *
     * @param string $name
     *
     * @return string|null
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getCookie($name)
    {
        // TODO: Implement getCookie() method.
    }

    /**
     * Returns last response status code.
     *
     * @return int
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getStatusCode()
    {
        // TODO: Implement getStatusCode() method.
    }

    /**
     * Returns last response content.
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getContent()
    {
        // TODO: Implement getContent() method.
    }

    /**
     * Capture a screenshot of the current window.
     *
     * @return string screenshot of MIME type image/* depending
     *                on driver (e.g., image/png, image/jpeg)
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getScreenshot()
    {
        // TODO: Implement getScreenshot() method.
    }

    /**
     * Return the names of all open windows.
     *
     * @return array array of all open windows
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getWindowNames()
    {
        // TODO: Implement getWindowNames() method.
    }

    /**
     * Return the name of the currently active window.
     *
     * @return string the name of the current window
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getWindowName()
    {
        // TODO: Implement getWindowName() method.
    }

    /**
     * Finds elements with specified XPath query.
     *
     * @param string $xpath
     *
     * @return NodeElement[]
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function find($xpath)
    {
        // TODO: Implement find() method.
    }

    /**
     * Returns element's tag name by it's XPath query.
     *
     * @param string $xpath
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getTagName($xpath)
    {
        // TODO: Implement getTagName() method.
    }

    /**
     * Returns element's text by it's XPath query.
     *
     * @param string $xpath
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getText($xpath)
    {
        // TODO: Implement getText() method.
    }

    /**
     * Returns element's inner html by it's XPath query.
     *
     * @param string $xpath
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getHtml($xpath)
    {
        // TODO: Implement getHtml() method.
    }

    /**
     * Returns element's outer html by it's XPath query.
     *
     * @param string $xpath
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getOuterHtml($xpath)
    {
        // TODO: Implement getOuterHtml() method.
    }

    /**
     * Returns element's attribute by it's XPath query.
     *
     * @param string $xpath
     * @param string $name
     *
     * @return string|null
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getAttribute($xpath, $name)
    {
        // TODO: Implement getAttribute() method.
    }

    /**
     * Returns element's value by it's XPath query.
     *
     * @param string $xpath
     *
     * @return string|bool|array
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::getValue
     */
    public function getValue($xpath)
    {
        // TODO: Implement getValue() method.
    }

    /**
     * Sets element's value by it's XPath query.
     *
     * @param string $xpath
     * @param string|bool|array $value
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::setValue
     */
    public function setValue($xpath, $value)
    {
        // TODO: Implement setValue() method.
    }

    /**
     * Checks checkbox by it's XPath query.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::check
     */
    public function check($xpath)
    {
        // TODO: Implement check() method.
    }

    /**
     * Unchecks checkbox by it's XPath query.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::uncheck
     */
    public function uncheck($xpath)
    {
        // TODO: Implement uncheck() method.
    }

    /**
     * Checks whether checkbox or radio button located by it's XPath query is checked.
     *
     * @param string $xpath
     *
     * @return Boolean
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::isChecked
     */
    public function isChecked($xpath)
    {
        // TODO: Implement isChecked() method.
    }

    /**
     * Selects option from select field or value in radio group located by it's XPath query.
     *
     * @param string $xpath
     * @param string $value
     * @param Boolean $multiple
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::selectOption
     */
    public function selectOption($xpath, $value, $multiple = false)
    {
        // TODO: Implement selectOption() method.
    }

    /**
     * Checks whether select option, located by it's XPath query, is selected.
     *
     * @param string $xpath
     *
     * @return Boolean
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::isSelected
     */
    public function isSelected($xpath)
    {
        // TODO: Implement isSelected() method.
    }

    /**
     * Clicks button or link located by it's XPath query.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function click($xpath)
    {
        // TODO: Implement click() method.
    }

    /**
     * Double-clicks button or link located by it's XPath query.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function doubleClick($xpath)
    {
        // TODO: Implement doubleClick() method.
    }

    /**
     * Right-clicks button or link located by it's XPath query.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function rightClick($xpath)
    {
        // TODO: Implement rightClick() method.
    }

    /**
     * Attaches file path to file field located by it's XPath query.
     *
     * @param string $xpath
     * @param string $path
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::attachFile
     */
    public function attachFile($xpath, $path)
    {
        // TODO: Implement attachFile() method.
    }

    /**
     * Checks whether element visible located by it's XPath query.
     *
     * @param string $xpath
     *
     * @return Boolean
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function isVisible($xpath)
    {
        // TODO: Implement isVisible() method.
    }

    /**
     * Simulates a mouse over on the element.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function mouseOver($xpath)
    {
        // TODO: Implement mouseOver() method.
    }

    /**
     * Brings focus to element.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function focus($xpath)
    {
        // TODO: Implement focus() method.
    }

    /**
     * Removes focus from element.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function blur($xpath)
    {
        // TODO: Implement blur() method.
    }

    /**
     * Presses specific keyboard key.
     *
     * @param string $xpath
     * @param string|int $char could be either char ('b') or char-code (98)
     * @param string $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function keyPress($xpath, $char, $modifier = null)
    {
        // TODO: Implement keyPress() method.
    }

    /**
     * Pressed down specific keyboard key.
     *
     * @param string $xpath
     * @param string|int $char could be either char ('b') or char-code (98)
     * @param string $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function keyDown($xpath, $char, $modifier = null)
    {
        // TODO: Implement keyDown() method.
    }

    /**
     * Pressed up specific keyboard key.
     *
     * @param string $xpath
     * @param string|int $char could be either char ('b') or char-code (98)
     * @param string $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function keyUp($xpath, $char, $modifier = null)
    {
        // TODO: Implement keyUp() method.
    }

    /**
     * Drag one element onto another.
     *
     * @param string $sourceXpath
     * @param string $destinationXpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function dragTo($sourceXpath, $destinationXpath)
    {
        // TODO: Implement dragTo() method.
    }

    /**
     * Executes JS script.
     *
     * @param string $script
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function executeScript($script)
    {
        // TODO: Implement executeScript() method.
    }

    /**
     * Evaluates JS script.
     *
     * The "return" keyword is optional in the script passed as argument. Driver implementations
     * must accept the expression both with and without the keyword.
     *
     * @param string $script
     *
     * @return mixed
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function evaluateScript($script)
    {
        // TODO: Implement evaluateScript() method.
    }

    /**
     * Waits some time or until JS condition turns true.
     *
     * @param int $timeout timeout in milliseconds
     * @param string $condition JS condition
     *
     * @return bool
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function wait($timeout, $condition)
    {
        // TODO: Implement wait() method.
    }

    /**
     * Submits the form.
     *
     * @param string $xpath Xpath.
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::submitForm
     */
    public function submitForm($xpath)
    {
        // TODO: Implement submitForm() method.
    }

    /**
     * @return string
     */
    protected function buildServerCmd()
    {
        // TODO Probably we can just do "NightmareServer <socket>" thanks to npm "bin" option... not sure though
        return sprintf(
            'node %s %s',
            escapeshellarg(__DIR__ . DIRECTORY_SEPARATOR . 'NightmareServer.js'),
            escapeshellarg($this->nodePort)
        );
    }

    /**
     * @param string $mtd
     * @param array $args
     * @return mixed
     * @throws DriverException
     */
    protected function sendAndWaitWithResult($mtd, $args = [])
    {
        $result = $this->nodeClient->call($mtd, $args);

        if (count($result) !== 1) {
            throw new DriverException(
                sprintf(
                    "Unexpected response from server; expected one result, not %d.\nMethod: %s\nArguments: %s\nResponse: %s",
                    count($result),
                    $mtd,
                    var_export($args, true),
                    var_export($result, true)
                )
            );
        }

        return $result[0];
    }

    /**
     * @param string $mtd
     * @param array $args
     * @throws DriverException
     */
    protected function sendAndWaitWithoutResult($mtd, $args = [])
    {
        $result = $this->nodeClient->call($mtd, $args);

        if (count($result) !== 0) {
            throw new DriverException(
                sprintf(
                    "Unexpected response from server; no result was not expected.\nMethod: %s\nArguments: %s\nResponse: %s",
                    count($result),
                    $mtd,
                    var_export($args, true),
                    var_export($result, true)
                )
            );
        }
    }
}
