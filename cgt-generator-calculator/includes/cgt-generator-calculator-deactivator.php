<?php

class Cgt_Generator_Calculator_Deactivator
{
    public static function deactivate()
    {
        // Remove the plugin version upon deactivation
        delete_option('cgt_generator_calculator_version');
    }
}
