import AppForm from '../app-components/Form/AppForm';

Vue.component('project-status-form-notificar', {
    mixins: [AppForm],
    props: ["project","user"],
    data: function() {
        return {
            form: {
                project_id:  this.project,
                stage_id:  2,
                user_id:  this.user,
                record:  '' ,

            },
            // mediaCollections: ['gallery']
        }
    }

});
