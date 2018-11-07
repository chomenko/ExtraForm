<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 01.06.2018 22:01
 */

namespace Chomenko\ExtraForm\Controls\Traits;


trait SizeInputs
{

    /**
     * @var string|null
     */
    protected $input_size;

    /**
     * @return $this
     */
    public function setSizeLG()
    {
        $this->input_size = "form-control-lg";
        return $this;
    }

    /**
     * @return $this
     */
    public function setSizeDefault()
    {
        $this->input_size = null;
        return $this;
    }

    /**
     * @return $this
     */
    public function setSizeSM()
    {
        $this->input_size = "form-control-sm";
        return $this;
    }

}