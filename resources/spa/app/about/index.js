// Make sure to import the styles for this module
import './about.scss';

import angular from 'angular';
// You might want to use common services and directives
// at some point, make sure to add it as a dependencies.
import MusicCommon from '../common';

// Import the config and controller.
import Config from './about.config';
import AboutController from './about.controller';

// Create the new module
let module = angular.module('Music.about', [
    // Add the common module as a dependency
    MusicCommon
])
// Setup the controller
    .controller('AboutController', AboutController)
    // Setup the configurations
    .config(Config)
    // We need this name to export it
    .name;

// Export the module so we can use it
// somewhere else.
export default module;