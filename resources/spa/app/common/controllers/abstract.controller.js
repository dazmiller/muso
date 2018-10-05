import UIRouter from 'angular-ui-router';

class AbstractController {

    constructor(names, injections){
        //Add injections to this instance
        for(let index in names){
            this[names[index]] = injections[index];
        }

        if(this.$state){
            let namespace = this.$state.current.name.split('.');
            switch(namespace[namespace.length - 1]){
                case 'add' :
                        this.add(this.$state.params);
                        break;
                case 'show':
                        this.show(this.$state.params);
                        break;
                default    :
                        this.index(this.$state.params);
            }
        }
    }

    index(){
        console.log('Index method not implemented');
    }

    show(){
        console.log('show method not implemented');
    }

    add(){
        console.log('add method not implemented');
    }

}

export default AbstractController;