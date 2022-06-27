import AppForm from '../app-components/Form/AppForm';

Vue.component('stage-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                name:  '' ,
                
            }
        }
    }

});