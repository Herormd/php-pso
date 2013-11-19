<?php

namespace Contrebis\Pso;


class WeightVector
{
    public $weights = array();

    public $n;

    public function __construct(array $weights)
    {
        $this->weights = array_values($weights);
        $this->n = count($this->weights);
    }

    /**
     * @param WeightVector $that
     * @return WeightVector
     * @throws \Exception
     */
    public function add(WeightVector $that)
    {
        if ($this->n !== $that->n) {
            throw new \Exception("Vector lengths don't match");
        }

        return new self(
            array_map(
                function ($a, $b) {
                    return $a + $b;
                },
                $this->weights,
                $that->weights
            )
        );
    }

    /**
     * @return WeightVector
     * @throws \Exception
     */
    public function negate()
    {
        return new self(
            array_map(
                function ($a) {
                    return -$a;
                },
                $this->weights
            )
        );
    }

    public function scalarMultiply($val)
    {
        return new self(
            array_map(
                function ($a) use ($val) {
                    return $val * $a;
                },
                $this->weights
            )
        );
    }

    /**
     * @param WeightVector $that
     * @return WeightVector
     * @throws \Exception
     */
    public function subtract(WeightVector $that)
    {
        return $this->add($that->negate());
    }

    /**
     * Constrain absolute values in each dimension to <= given value
     *
     * @param float $minVal A minimum value
     * @param float $maxVal A maximum value
     * @return WeightVector
     */
    public function constrain($minVal, $maxVal)
    {
        return new self(
            array_map(
                function ($x) use ($minVal, $maxVal) {
                    return $x < $minVal ? $minVal :
                        ($x > $maxVal ? $maxVal : $x);
                },
                $this->weights
            )
        );
    }

    public function __toString()
    {
        return json_encode($this->weights);
    }
}
