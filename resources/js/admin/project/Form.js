import AppForm from '../app-components/Form/AppForm';
import axios from 'axios';

Vue.component('project-form', {
  mixins: [AppForm],
  props: ['sat', 'modalidad', 'departamentos', 'data','tierra','tipologias'],
  data() {
    return {
      form: {
        name: this.data?.name || '',
        phone: this.data?.phone || '',
        // inicializamos null y la asignamos en created() para depurar mejor
        sat_id: null,
        modalidad_id: this.data?.modalidad_id || '',
        land_id: this.data?.land_id || '',
        typology_id: this.data?.typology_id || '',
        leader_name: this.data?.leader_name || '',
        state_id: this.data?.state_id || '',
        city_id: this.data?.city_id || '',
        localidad: this.data?.localidad || '',
        action: this.data?.action || '',
        expsocial: this.data?.expsocial || '',
        exptecnico: this.data?.exptecnico || ''
      },
      tierraOptions: [],
      tipologiaOptions: [],
      localidadOptions: [],
      stateOptions: this.departamentos || [],
    };
  },
  created() {
    // --- helpers de normalización ---
    const normalize = (v) => {
      if (v === null || v === undefined) return null;
      const s = String(v).trim();
      // intenta extraer dígitos (ej: "SAT 239" => 239, "120     " => 120)
      const m = s.match(/\d+/);
      if (m) return Number(m[0]);
      // si no hay dígitos, devuelve trim en minúsculas para comparar strings
      return s.toLowerCase();
    };

    const findSatObject = (rawProjectSat) => {
      const normProject = normalize(rawProjectSat);
      // intento de match: si proyecto tiene número, comparar con número; sino comparar string
      return this.sat.find(s => {
        const sCode = normalize(s.NucCod); // NucCod viene con espacios o prefijos
        if (typeof normProject === 'number' && typeof sCode === 'number') {
          return normProject === sCode;
        }
        return String(sCode) === String(normProject);
      }) || null;
    };

    // --- logs iniciales para depuración ---
    console.log('--- DEBUG sat binding ---');
    console.log('prop sat (lista completa) length:', this.sat?.length);
    console.log('prop data (project):', this.data);
    console.log('raw project.sat_id (tal como viene):', this.data?.sat_id, ' typeof:', typeof this.data?.sat_id);

    // intento de encontrar el objeto SAT que corresponde al valor guardado
    const found = findSatObject(this.data?.sat_id);
    console.log('sat encontrado (initial):', found);

    // Asignamos al form.sat_id el OBJETO (para que el multiselect muestre el texto).
    // Backend ya acepta objeto (tu Request ya extrae NucCod), así que guardamos el objeto.
    this.form.sat_id = found;

    // si no se encontró, mostramos comparaciones para entender por qué
    if (!found) {
      const normProject = normalize(this.data?.sat_id);
      console.warn('No se encontró SAT. Normalizaciones:');
      console.log('normalized project.sat_id =>', normProject);
      // mostrar la lista mapeada a valores normalizados (útil si hay muchas opciones)
      const mapped = (this.sat || []).map(s => ({
        raw: s.NucCod,
        normalized: normalize(s.NucCod)
      }));
      console.log('lista de NucCod normalizados (primeros 50):', mapped.slice(0, 50));
    }

    // --- Inicializar campos dependientes en modo edit ---
    this.initializeDependentFields();
  },

  methods: {
    async initializeDependentFields() {
      console.log('--- Inicializando campos dependientes ---');
      console.log('Valores del proyecto:');
      console.log('  modalidad_id:', this.form.modalidad_id, typeof this.form.modalidad_id);
      console.log('  land_id:', this.form.land_id, typeof this.form.land_id);
      console.log('  typology_id:', this.form.typology_id, typeof this.form.typology_id);
      console.log('  state_id:', this.form.state_id, typeof this.form.state_id);
      console.log('  city_id:', this.form.city_id, typeof this.form.city_id);

      // Si hay modalidad_id, cargar terrenos
      if (this.form.modalidad_id) {
        await this.loadTierraOptions(this.form.modalidad_id);
      }

      // Si hay land_id, cargar tipologías
      if (this.form.land_id) {
        await this.loadTipologiaOptions(this.form.land_id);
      }

      // Si hay state_id, cargar localidades
      if (this.form.state_id) {
        await this.loadLocalidadOptions(this.form.state_id);
      }
    },

    async loadTierraOptions(modalidadId) {
      if (!modalidadId) {
        this.tierraOptions = [];
        return;
      }

      try {
        console.log('Cargando terrenos para modalidad:', modalidadId);
        // Usar la ruta AJAX existente
        const response = await axios.get(`/admin/projects/ajax/${modalidadId}/lands`);
        console.log('Respuesta completa terrenos:', response);
        console.log('response.data:', response.data);

        // Convertir objeto a array de objetos {id, name}
        if (response.data && typeof response.data === 'object') {
          this.tierraOptions = Object.entries(response.data).map(([key, value]) => ({
            id: parseInt(key),
            name: value.toString().trim()
          }));
        } else {
          this.tierraOptions = [];
        }

        console.log('Terrenos cargados (final):', this.tierraOptions);

        // Si el proyecto tiene un land_id que ya no es válido para esta modalidad, limpiarlo
        if (this.form.land_id && !this.tierraOptions.find(t => t.id == this.form.land_id)) {
          console.log('Land_id no válido para modalidad, limpiando...');
          this.form.land_id = '';
          this.form.typology_id = ''; // También limpiar tipología
          this.tipologiaOptions = [];
        }
      } catch (error) {
        console.error('Error cargando terrenos:', error);
        console.log('Error details:', error.response);
        this.tierraOptions = [];
      }
    },

    async loadTipologiaOptions(landId) {
      if (!landId) {
        this.tipologiaOptions = [];
        return;
      }

      try {
        console.log('Cargando tipologías para terreno:', landId);
        // Usar la ruta AJAX existente
        const response = await axios.get(`/admin/projects/ajax/${landId}/typology`);
        console.log('Respuesta completa tipologías:', response);
        console.log('response.data tipologías:', response.data);

        // Convertir objeto a array de objetos {id, name}
        if (response.data && typeof response.data === 'object') {
          this.tipologiaOptions = Object.entries(response.data).map(([key, value]) => ({
            id: parseInt(key),
            name: value.toString().trim()
          }));
        } else {
          this.tipologiaOptions = [];
        }

        console.log('Tipologías cargadas (final):', this.tipologiaOptions);

        // Si el proyecto tiene un typology_id que ya no es válido para este terreno, limpiarlo
        if (this.form.typology_id && !this.tipologiaOptions.find(t => t.id == this.form.typology_id)) {
          console.log('Typology_id no válido para terreno, limpiando...');
          this.form.typology_id = '';
        }
      } catch (error) {
        console.error('Error cargando tipologías:', error);
        console.log('Error details tipologías:', error.response);
        this.tipologiaOptions = [];
      }
    },

    async loadLocalidadOptions(stateId) {
      if (!stateId) {
        this.localidadOptions = [];
        return;
      }

      try {
        console.log('Cargando localidades para departamento:', stateId);
        console.log('Proyecto city_id actual:', this.form.city_id, 'tipo:', typeof this.form.city_id);

        // Usar la ruta AJAX existente
        const response = await axios.get(`/admin/projects/ajax/${stateId}/local`);
        console.log('Respuesta completa localidades:', response);
        console.log('response.data localidades:', response.data);

        // Convertir objeto a array de objetos {id, name}
        if (response.data && typeof response.data === 'object') {
          this.localidadOptions = Object.entries(response.data).map(([key, value]) => ({
            id: parseInt(key),
            name: value.toString().trim()
          }));
        } else {
          this.localidadOptions = [];
        }

        console.log('Localidades cargadas (final):', this.localidadOptions);
        console.log('IDs disponibles:', this.localidadOptions.map(l => l.id));

        // Debug: verificar si el city_id existe
        if (this.form.city_id) {
          const found = this.localidadOptions.find(l => l.id == this.form.city_id);
          console.log('¿Se encontró city_id', this.form.city_id, '?:', found);

          if (!found) {
            console.log('City_id no válido para departamento, limpiando...');
            console.log('Comparación detallada:');
            this.localidadOptions.forEach(l => {
              console.log(`  ${l.id} == ${this.form.city_id}? ${l.id == this.form.city_id}`);
            });
            this.form.city_id = '';
          }
        }
      } catch (error) {
        console.error('Error cargando localidades:', error);
        console.log('Error details localidades:', error.response);
        this.localidadOptions = [];
      }
    },

    // antes de submit (si usas AppForm submit), asegúrate que se envíe la forma esperada:
    prepareForSubmit() {
      // si backend espera objeto con NucCod en sat_id, no hacer nada;
      // si backend espera sólo número, convertir:
      const payload = { ...this.form };
      if (payload.sat_id && payload.sat_id.NucCod) {
        // extraer y enviar número (normalizado)
        const match = String(payload.sat_id.NucCod).match(/\d+/);
        payload.sat_id = match ? Number(match[0]) : String(payload.sat_id.NucCod).trim();
      }
      // ... convertir modalidad/land/typology si manejas objetos similares ...
      return payload;
    }
  },

  watch: {
    // Si el array 'sat' llega luego, reintento la búsqueda
    sat(newSat) {
      console.log('watch sat: cambiaron las opciones SAT, reintentando match...');
      const normalize = (v) => {
        if (v === null || v === undefined) return null;
        const s = String(v).trim();
        const m = s.match(/\d+/);
        if (m) return Number(m[0]);
        return s.toLowerCase();
      };
      const normProject = normalize(this.data?.sat_id);
      const found = (newSat || []).find(s => {
        const sCode = normalize(s.NucCod);
        if (typeof normProject === 'number' && typeof sCode === 'number') return normProject === sCode;
        return String(sCode) === String(normProject);
      }) || null;
      console.log('sat encontrado (watch):', found);
      if (found) this.form.sat_id = found;
    },

    // Watch para modalidad - cargar terrenos
    'form.modalidad_id'(newVal, oldVal) {
      console.log('Modalidad cambió de', oldVal, 'a', newVal);

      if (newVal !== oldVal) {
        // Limpiar campos dependientes
        this.form.land_id = '';
        this.form.typology_id = '';
        this.tipologiaOptions = [];

        // Cargar nuevos terrenos via AJAX
        this.loadTierraOptions(newVal);
      }
    },

    // Watch para terreno - cargar tipologías
    'form.land_id'(newVal, oldVal) {
      console.log('Terreno cambió de', oldVal, 'a', newVal);

      if (newVal !== oldVal) {
        // Limpiar tipología
        this.form.typology_id = '';

        // Cargar nuevas tipologías via AJAX
        this.loadTipologiaOptions(newVal);
      }
    },

    // Watch para departamento - cargar localidades
    'form.state_id'(newVal, oldVal) {
      console.log('Departamento cambió de', oldVal, 'a', newVal);

      if (newVal !== oldVal) {
        // Limpiar ciudad
        this.form.city_id = '';

        // Cargar nuevas localidades via AJAX
        this.loadLocalidadOptions(newVal);
      }
    }
  }
});
