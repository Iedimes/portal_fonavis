import AppForm from '../app-components/Form/AppForm';

Vue.component('project-status-form-eliminar', {
    mixins: [AppForm],
    props: ["project","user","stages"],
    data: function() {
        return {
            form: {
                project_id:  this.project,
                user_id:  this.user,
                record:  '' ,

            }
        }
    }

});
