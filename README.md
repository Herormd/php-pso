# Experimental Particle Swarm Optimisation component

More information TBC.

If you have any suggestions on improvements to this project they would be gratefully received.


## Basic usage

    use Contrebis\Pso\Swarm;
    use Contrebis\Pso\WeightVector;

    $constants = array(
        'phi1' => 0.15,
        'phi2' => 0.15,
        'vMax' => 0.5,
        'inertia' => 0.97,
    );
    $fitnessFunc = function (WeightVector $x) {
        // insert your fitness function here
        // too lazy to think of a good example now!
        // high values are good, low values are bad
        return 0;
    };
    $swarm = new Swarm($constants, 10, 2, $fitnessFunc);

    for ($i = 0; $i < $iterations; $i++) {
        $swarm->go();
    }

    echo "$swarm";

## TODO

- Add tests
- Refactor out responsibility for creating initial positions and velocities
- Pick and add a license

