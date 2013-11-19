<?php

namespace Contrebis\Pso;


class Particle
{
    /**
     * @var WeightVector Position
     */
    protected $x;

    /**
     * @var WeightVector Velocity
     */
    protected $v;

    /**
     * @var WeightVector Personal best
     */
    protected $p;

    /**
     * @var number Personal best value
     */
    protected $p_val = -1000000;

    /**
     * @var callable Fitness function - takes WeightVector as a parameter (eg current position)
     */
    protected $f;

    protected $fitness;

    protected $defaults = array(
        'phi1' => 1,
        'phi2' => 1,
        'inertia' => 1,
        'vMax' => null,
    );

    /**
     * @var Swarm
     */
    public $swarm;

    public function __construct(array $constants, WeightVector $x, WeightVector $v, Swarm $swarm, callable $f)
    {
        $constants = array_merge($this->defaults, $constants);
        $this->phi1 = $constants['phi1'];
        $this->phi2 = $constants['phi2'];
        $this->vMax = $constants['vMax'];
        $this->inertia = $constants['inertia'];

        $this->x = $x;
        $this->fitness = $f($x);

        $this->v = $v;
        $this->swarm = $swarm;
        $this->f = $f;

        $this->p = $x;
        $this->p_val = $f($x);
    }

    public function fitness()
    {
        return $this->fitness;
    }

    /**
     * Get/set best solution
     *
     * @param WeightVector $v
     * @return WeightVector
     */
    public function best(WeightVector $v = null)
    {
        if ($v) {
            $fitness = $this->f;
            $this->p = $v;
            $this->p_val = $fitness($v);
        }

        return $this->p;
    }

    public function bestVal()
    {
        return $this->p_val;
    }

    /**
     * Get/set current position vector
     *
     * @param WeightVector $x
     * @return WeightVector
     */
    public function position(WeightVector $x = null)
    {
        if ($x) {
            $fitness = $this->f;
            $this->x = $x;
            $this->fitness = $fitness($x);
        }

        return $this->x;
    }

    public function go()
    {
        if ($this->fitness() > $this->p_val) {
            $this->best($this->x);
        }

        if ($this->fitness() > $this->swarm->bestVal()) {
            $this->swarm->best($this);
        }

        $phi1 = $this->phi1 * lcg_value();
        $phi2 = $this->phi2 * lcg_value();
        $pg = $this->swarm->best()->position();

        // v 0 i = v i + j 1 _ (p i - x i ) + j 2 _ (p g - x i )
        $this->v = $this->v->scalarMultiply($this->inertia)
            ->add(
                $this->p->subtract($this->x)->scalarMultiply($phi1)
            )
            ->add(
                $pg->subtract($this->x)->scalarMultiply($phi2)
            )->constrain(-$this->vMax, $this->vMax);

        // x i = x i + v i
        $this->position($this->x->add($this->v));
    }

    public function __toString()
    {
        return $this->x . " f(x) -> " . $this->fitness();
    }
}
