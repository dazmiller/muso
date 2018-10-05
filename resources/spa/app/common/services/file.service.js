
/**
 * @TODO: Remove this file?
 * I don't think we are using it anywhere else! Now we are using
 * the common/utils/connection.js for this.
 */
class File{
    buildForm(params){
        let formData = new FormData(),
            value,
            field,
            date;

        if(params){
            for(field in params){
                if(params[field] !== undefined && params[field] !== null){
                    value = params[field];

                    if(value instanceof Date){
                        value = [value.getFullYear(),value.getMonth()+1,value.getDate()].join('-');
                    }

                    if(value !== null || value !== undefined){
                        formData.append(field, value);
                    }
                }
            }
        }
        return formData;
    }
}

File.$inject = [];

export default File;