import AppForm from '../app-components/Form/AppForm';
Vue.component('assignment-form', {
    mixins: [AppForm],
    props:['document','category','pt','stage'],

    data: function() {
        return {
            form: {
                document:  '' ,
                category:  '' ,
                project_type:  '' ,
                stage:  '' ,

            }
        }
    }

});
