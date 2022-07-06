import AppForm from '../app-components/Form/AppForm';

Vue.component('user-form', {
    mixins: [AppForm],
    props:['sat'],
    methods: {
        customLabel({ NucCod, NucNomSat }) {
          return `${NucCod} – ${NucNomSat}`;
        }
      },
    data: function() {
        return {
            form: {
                name:  '' ,
                email:  '' ,
                username:  '' ,
                password:  '' ,
                sat:  '' ,

            }
        }
    }

});
