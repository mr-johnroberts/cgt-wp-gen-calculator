<?php

class Cgt_Generator_Calculator_Activator
{
    public static function activate()
    {
        // Set the plugin version upon activation
        update_option('cgt_generator_calculator_version', CGT_GENERATOR_CALCULATOR_VERSION);
    }
}
