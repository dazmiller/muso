

let PaginatorService = function(){
    let page,
        loaded;

    this.start = () => {
        page = 0;
        loaded = {};
    }
    
    /**
     * When using the laravel paginator,
     * we need to start from page 1
     */
    this.reset = () => {
        page = 1;
        loaded = {};
    }

    this.setPage = (p)=>{
        page = p;
    }

    this.getPage = ()=>{
        return page;
    }

    this.nextPage = () => {
        page++;
    }

    this.getLoaded = ()=>{
        return loaded;
    }

    this.addLoaded = (height, p)=>{
        loaded[height] = p;
    }

    this.isLoaded = (height)=>{
        return !!loaded[height];
    }

    this.start();
}

PaginatorService.$inject = [];

export default PaginatorService;
