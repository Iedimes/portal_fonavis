import AppForm from '../app-components/Form/AppForm';

Vue.component('admin-users-dependency-form', {
    mixins: [AppForm],
    props:['admin_user','dependency','data'],

    data: function() {
        return {
            form: {
                admin_user_id:  '' ,
                dependency_id:  '' ,

            }
        }
    },

    computed: {
        adminUserObject: {
            get() {
                if (this.form.admin_user_id && this.admin_user) {
                    return this.admin_user.find(u => u.id === this.form.admin_user_id);
                }
                return '';
            },
            set(value) {
                this.form.admin_user_id = value ? value.id : '';
            }
        },
        dependencyObject: {
            get() {
                if (this.form.dependency_id && this.dependency) {
                    return this.dependency.find(d => d.id === this.form.dependency_id);
                }
                return '';
            },
            set(value) {
                this.form.dependency_id = value ? value.id : '';
            }
        }
    },

    methods: {
        nameWithEmail(option) {
            return option.first_name + ' ' + option.last_name + ' (' + option.email + ')';
        }
    }

});
