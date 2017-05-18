<?php
/**
 * Created by PhpStorm.
 * User: rashed
 * Date: 16-May-17
 * Time: 4:08 PM
 */

namespace Rashidul\RainDrops\Html;

if (!defined('ENT_HTML5'))
{
    define('ENT_HTML5', 48);
}


class Element extends Markup
{


    /** @var int The language convention used for XSS avoiding */
    public static $outputLanguage = ENT_HTML5;
    protected $autocloseTagsList = array(
        'img', 'br', 'hr', 'input', 'area', 'link', 'meta', 'param'
    );
    /**
     * Shortcut to set('id', $value)
     * @param string $value
     * @return Element
     */
    public function id($value)
    {
        return $this->set('id', $value);
    }
    /**
     * Add a class to classList
     * @param string $value
     * @return Element
     */
    public function addClass($value)
    {
        if (!isset($this->attributeList['class']) || is_null($this->attributeList['class'])) {
            $this->attributeList['class'] = array();
        }
        $this->attributeList['class'][] = $value;
        return $this;
    }


    /**
     * Shortcut to set name attribute
     *
     * @param $value
     * @return Markup
     */
    public function setName($value)
    {
        return $this->set('name', $value);
    }

    /**
     * Shortcut to set type attribute
     *
     * @param $value
     * @return Markup
     */
    public function setType($value)
    {
        // TODO.
        // ignore if its not a form input element
        return $this->set('type', $value);
    }

    /**
     * Shortcut to set required attribute
     *
     * @param $isRequired
     * @return Markup
     * @internal param $value
     */
    public function setRequired($isRequired)
    {
        if ( $isRequired ) {

            return $this->set('required', true);

        }

        return $this;
    }

    /**
     * Shortcut to set selected attribute
     *
     * @param $isRequired
     * @return Markup
     * @internal param $value
     */
    public function setSelected($isSelected)
    {
        if ( $isSelected ) {

            return $this->set('selected', true);

        }

        return $this;
    }

    /**
     * Shortcut to set selected attribute
     *
     * @param $value
     * @return Markup
     * @internal param $isRequired
     * @internal param $value
     */
    public function setValue($value)
    {

        return $this->set('value', $value);

    }

    /**
     * Shortcut to set required attribute
     *
     * @param $isRequired
     * @return Markup
     * @internal param $value
     */
    public function setDisabled($isDisabled)
    {
        if ( $isDisabled ) {

            return $this->set('disabled', true);

        }

        return $this;
    }


    /**
     * Remove a class from classList
     * @param string $value
     * @return Element
     */
    public function removeClass($value)
    {
        if (!is_null($this->attributeList['class'])) {
            unset($this->attributeList['class'][array_search($value, $this->attributeList['class'])]);
        }
        return $this;
    }

    /**
     * Render the element as string
     */
    public function render()
    {
        return $this->toString();
    }


}