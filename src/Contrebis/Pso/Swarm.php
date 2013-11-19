<?php

namespace Contrebis\Pso;


class Swarm
{
    public $phi_1;
    public $phi_2;

    protected $vMax = 0.5;

    /**
     * @var WeightVector Global best
     */
    protected $g;

    /**
     * @var number Global best value
     */
    protected $g_val = -10000000;

    /**
     * @var callable Fitness function
     */
    protected $f;

    /**
     * @var Particle[]
     */
    public $particles = array();

    protected $constants;

    public function __construct(array $constants, $numParticles, $n, callable $fitnessFunc)
    {
        $this->f = $fitnessFunc;

        for ($i = 0; $i < $numParticles; $i++) {
            $particle = $this->createParticle($constants, $n, $fitnessFunc);
            $this->particles[] = $particle;
            if ($particle->fitness() > $this->bestVal()) {
                $this->best($particle);
            }
        }
    }

    protected function createParticle(array $constants, $n, callable $fitnessFunc)
    {
        return new Particle($constants, $this->createVector($n, -5, 15), $this->createVector($n, -1, 1), $this, $fitnessFunc);
    }

    protected function createVector($n, $min, $max)
    {
        return new WeightVector(
            array_map(
                function () use ($min, $max) {
                    return ($max - $min) * lcg_value() + $min;
                },
                array_fill(0, $n, 0)
            )
        );
    }

    public function go()
    {
        foreach ($this->particles as $particle) {
            $particle->go();
        }
    }

    /**
     * Get or set the particle with the global best for the swarm
     *
     * @param Particle $p Supply parameter to update
     * @return Particle
     */
    public function best(Particle $p = null)
    {
        if ($p) {
            $this->g = $p;
            $this->g_val = $p->bestVal();
        }

        return $this->g;
    }

    /**
     * @return number The fitness of the global best
     */
    public function bestVal()
    {
        return $this->g_val;
    }

    public function __toString()
    {
        return array_reduce(
            $this->particles,
            function ($aggr, $p) {
                return $aggr . "$p" . ($p === $this->best() ? ' <- global best' : '') . "\n";
            }
        );
    }
}
