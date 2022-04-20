<template>

    <div>
        <v-card elevation="2" :loading="loading">
            <v-card-title
          >
          <v-badge
                :content="clasificaciones.length"
                :value="clasificaciones.length"
                color="green"

            >
            Clasificación de Actividades
            </v-badge>
          <v-spacer></v-spacer>
          <v-col cols="auto">
              <v-text-field
              v-model="search"
              append-icon="mdi-magnify"
              label="Buscar"
              single-line
              hide-details
              filled
            rounded
            dense
          ></v-text-field>
          </v-col>

          <v-btn
              class="mx-2"
              fab
              dark
              small
              color="primary"
              @click="addClasificacion"
          >
              <v-icon dark>
                  mdi-plus
              </v-icon>
          </v-btn>
        </v-card-title>

            <v-card-text>
                <v-data-table
                    :headers="headers"
                    :items="clasificaciones"
                    :search="search"
                >
                <template v-slot:item="row">
                    <tr>
                        <td>{{row.item.nombre}}</td>
                        <td>{{row.item.descripcion}}</td>
                        <td><v-chip small dark :style="`background:${row.item.color}`">{{row.item.color}}</v-chip></td>
                        <td>
                             <v-chip v-if="row.item.estado==1"
                            class="ma-2"
                            color="success"
                            small
                            >Habilitada
                            </v-chip>
                            <v-chip v-else
                            class="ma-2"
                            color="error"
                            small
                            >Deshabilitada
                            </v-chip>
                        </td>
                        <td>
                            <v-btn  icon color="primary" @click="editClasificacion(row.item)">
                                <v-icon dark>mdi-pencil</v-icon>
                                </v-btn>
                                <v-btn  icon color="error" @click="deleteClasificacion(row.item)">
                                <v-icon dark>mdi-delete</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                </template>
      </v-data-table>
            </v-card-text>

            <v-card-actions> </v-card-actions>
        </v-card>
        <template>
            <v-row justify="center">
                <v-dialog v-model="dialog" persistent max-width="400px">
                    <v-card>
                        <v-card-title>
                            <span class="headline">{{ titleForm }}</span>
                        </v-card-title>
                        <v-card-text>
                                <v-form @submit.prevent="createClasificacion"> </v-form>
                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field
                                            v-model="clasificacion.nombre"
                                            label="Nombre de la clasificación*"
                                            required
                                        ></v-text-field>
                                    </v-col>

                                    <v-col cols="12">
                                        <v-text-field
                                            v-model="clasificacion.descripcion"
                                            label="Descripcion de la clasificación"
                                            required
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-select :items="estado"
                                            v-model="clasificacion.estado"
                                                label="Estado" >
                                            <template slot="selection" slot-scope="data">
                                                <!-- HTML that describe how select should render selected items -->
                                                {{ data.item.text }}
                                            </template>
                                            <template slot="item" slot-scope="data">
                                                <!-- HTML that describe how select should render items when the select is open -->
                                                {{ data.item.text }}
                                            </template>
                                        </v-select>
                                    </v-col>
                                <v-col cols="12">
                                    <v-color-picker
                                    dot-size="28"
                                    hide-canvas
                                    hide-inputs
                                    mode="hexa"
                                    show-swatches
                                    swatches-max-height="100"
                                    v-model="color"

                                    ></v-color-picker>
                                </v-col>
                                </v-row>
                            <small>*indicates required field</small>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn
                                color="error"
                                text
                                @click="dialog = false"
                            >
                                Cerrar
                            </v-btn>
                            <v-btn
                                v-if="!update"
                                color="primary"
                                text
                                @click="createClasificacion"
                            >
                                Guardar
                            </v-btn>
                            <v-btn
                                v-else
                                color="primary"
                                text
                                @click="updateClasificacion"
                            >
                                Actualizar
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-row>
        </template>

    </div>

</template>

<script>

export default {
    data() {
        return {
            showScheduleForm: false,
            dialog: false,
            update: false,
            clasificacion: {},
            clasificaciones: [],
            loading: true,
            titleForm: null,
            search: "",
            estado: [
                { text: "HABILITADA", value: 1 },
                { text: "DESHABILITADA", value: 0 },
            ],
            headers: [
                { text: "Nombre", value: "nombre" },
                { text: "Descripcion", value: "descripcion" },
                { text: "Color", value: "color" ,sortable: false, filterable:false},
                { text: "Estado", value: "estado" },
                { text: "Acciones", sortable: false }
            ],
            color:{}
        };
    },
    created() {

        this.initialData();
    },
    methods: {
        initialData(){
            this.axios.get("/api/clasificacion/").then(response => {
            this.clasificaciones = response.data;
            this.loading = false;

        }).catch((err)=> {
                    this.$swal.fire({
                                title: 'Error',
                                html: `${err}`,
                                icon: 'error'
                                })
                }).finally();
        },
        createClasificacion() {
            this.clasificacion.color=this.color.hex
            console.log(this.clasificacion);
            this.loading = true;
            this.axios
                .post("/api/clasificacion", this.clasificacion)
                .then(() => {
                    this.dialog = false;
                    this.initialData();

                }).catch((err)=> {
                    this.$swal.fire({
                                title: 'Error',
                                html: `${err}`,
                                icon: 'error'
                                })
                }).finally();
        },
        addClasificacion() {
            this.titleForm = "Nueva Clasificación";
            this.area = {};
            this.update = false;
            this.dialog = true;
        },
        editClasificacion(el) {
            this.titleForm = "Editar Clasificación";
            this.update = true;
            this.clasificacion.id = el.id;
            this.clasificacion.nombre = el.nombre;
            this.clasificacion.descripcion = el.descripcion;
            this.clasificacion.estado = el.estado;
            this.color = (el.color);
            this.dialog = true;
        },
        updateClasificacion() {
            console.log(this.color);
            this.clasificacion.color=this.color
            this.loading = true;
            this.axios
                .patch(`/api/clasificacion/${this.clasificacion.id}`, this.clasificacion)
                .then(res => {
                    this.dialog = false;
                    this.initialData();
                }).catch((err)=> {
                    this.$swal.fire({
                                title: 'Error',
                                html: `${err}`,
                                icon: 'error'
                                })
                }).finally();
        },
        deleteClasificacion(el) {
            this.loading = true;
            this.axios.delete(`/api/clasificacion/${el.id}`).then(() => {
                let i = this.clasificaciones.map(data => data.id).indexOf(el.id);
                this.clasificaciones.splice(i, 1);
                this.loading = false;
            }).catch((err)=> {
                    this.$swal.fire({
                                title: 'Error',
                                html: `${err}`,
                                icon: 'error'
                                })
                }).finally();
        }
    }
};
</script>
