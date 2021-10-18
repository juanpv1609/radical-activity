<template>

    <div>
        <v-card elevation="2" :loading="loading">
            <v-card-title
          >
          <v-badge
                :content="tipos.length"
                :value="tipos.length"
                color="green"

            >
            Tipos de Actividad
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
              @click="addTipoActividad"
          >
              <v-icon dark>
                  mdi-plus
              </v-icon>
          </v-btn>
        </v-card-title>

            <v-card-text>
                <v-data-table
                    :headers="headers"
                    :items="tipos"
                    :search="search"
                >
                <template v-slot:item="row">
                    <tr>
                        <td>{{row.item.descripcion}}</td>
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
                            <v-btn  icon color="primary" @click="editTipoActividad(row.item)">
                                <v-icon dark>mdi-pencil</v-icon>
                                </v-btn>
                                <v-btn  icon color="error" @click="deleteTipoActividad(row.item)">
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
                            <v-container>

                                <v-row>
                                    <v-col cols="12" >
                                        <v-text-field
                                            v-model="tipo.descripcion"
                                            label="Descripción del Tipo de actividad"
                                            required
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" >
                                        <v-select :items="estado"
                                            v-model="tipo.estado"
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
                                </v-row>
                            </v-container>
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

                                @click="createTipoActividad"
                            >
                                Guardar
                            </v-btn>
                            <v-btn
                                v-else
                                color="primary"

                                @click="updateTipoActividad"
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
            tipo: {},
            tipos: [],
            loading: true,
            titleForm: null,
            search: "",
            estado: [
                { text: "HABILITADA", value: 1 },
                { text: "DESHABILITADA", value: 0 },
            ],
            headers: [
                { text: "Descripcion", value: "descripcion" },
                { text: "Estado", value: "estado" },
                { text: "Acciones", sortable: false }
            ]
        };
    },
    created() {

        this.axios.get("/api/tipo-actividad/").then(response => {
            this.tipos = response.data;
            this.loading = false;

        });
    },
    methods: {
        createTipoActividad() {
            this.loading = true;
            console.log(this.tipo);
             this.axios
                .post("/api/tipo-actividad", this.tipo)
                .then(() => {
                    this.dialog = false;
                    this.axios.get("/api/tipo-actividad/").then(response => {
                        this.tipos = response.data;
                        this.loading = false;

                    });
                })
                .catch(err => console.log(err))
                .finally(() => (this.loading = false));
        },
        addTipoActividad() {
            this.titleForm = "Nuevo Tipo de actividad";
            this.tipo = {};
            this.update = false;
            this.dialog = true;
        },
        editTipoActividad(el) {
            this.titleForm = "Editar Tipo de actividad";
            this.update = true;
            this.tipo.id = el.id;
            this.tipo.descripcion = el.descripcion;
            this.tipo.estado = el.estado;
            this.dialog = true;
        },
        updateTipoActividad() {
            this.loading = true;
            this.axios
                .patch(`/api/tipo-actividad/${this.tipo.id}`, this.tipo)
                .then(res => {
                    this.dialog = false;
                    this.axios.get("/api/tipo-actividad/").then(response => {
                        this.tipos = response.data;
                        this.loading = false;
                    });
                });
        },
        deleteTipoActividad(el) {
            this.loading = true;
            this.axios.delete(`/api/tipo-actividad/${el.id}`).then(() => {
                let i = this.areas.map(data => data.id).indexOf(el.id);
                this.tipos.splice(i, 1);
                this.loading = false;
            });
        }
    }
};
</script>
