import AbstractController from '../common/controllers/abstract.controller';

/**
 * This page is basically to show the available icons in the app
 */
class IconsController extends AbstractController{

    constructor($rootScope, $state){
        super(IconsController.$inject, arguments);
    }
    
    index() {
        console.log('icon index page!');
    }
}

IconsController.$inject= ['$rootScope', '$state'];

export default IconsController;