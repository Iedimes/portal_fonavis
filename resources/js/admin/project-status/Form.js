import AppForm from '../app-components/Form/AppForm';

Vue.component('project-status-form', {
    mixins: [AppForm],
    props: ["project", "user", "stages", "dependencia"], // agregamos dependencia
    data: function() {
        return {
            form: {
                project_id: this.project,
                stage: '',
                user_id: this.user,
                record: '',
                dependencia: this.dependencia // importante: ahora la envías al backend
            },
            mediaCollections: ['gallery']
        }
    }
});
