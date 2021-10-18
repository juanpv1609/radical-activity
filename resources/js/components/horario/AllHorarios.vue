<template>

    <div>
        <v-card elevation="2" :loading="loading">
            <v-card-title
          >
          <v-badge
                :content="horarios.length"
                :value="horarios.length"
                color="green"

            >
            Horarios
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
              @click="addHorario"
          >
              <v-icon dark>
                  mdi-plus
              </v-icon>
          </v-btn>
        </v-card-title>

            <v-card-text>
                <v-data-table
                    :headers="headers"
                    :items="horarios"
                    :search="search"
                >
                <template v-slot:item="row">
                    <tr>
                        <td>{{row.item.id}}</td>
                        <td>{{row.item.nombre}}</td>
                        <td>{{row.item.descripcion}}</td>
                        <td>{{row.item.inicio}}</td>
                        <td>{{row.item.fin}}</td>
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
                            <v-btn  icon color="primary" @click="editHorario(row.item)">
                                <v-icon dark>mdi-pencil</v-icon>
                                </v-btn>
                                <v-btn  icon color="error" @click="deleteHorario(row.item)">
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
                <v-dialog v-model="dialog" persistent max-width="800px">
                    <v-card>
                        <v-card-title>
                            <span class="headline">{{ titleForm }}</span>
                        </v-card-title>
                        <v-card-text>
                            <v-container>

                                <v-row>
                                    <v-col cols="12" sm="6">
                                        <v-text-field
                                            v-model="horario.nombre"
                                            label="Nombre del horario*"
                                            required
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-text-field
                                            v-model="horario.descripcion"
                                            label="Descripción"
                                            required
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <h2>Inicio:</h2>
                                        <v-time-picker
                                        format="24hr"
                                        v-model="horario.inicio"
                                        :max="end"
                                        ></v-time-picker>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <h2>Fin:</h2>
                                        <v-time-picker
                                        format="24hr"
                                        v-model="horario.fin"
                                        :min="horario.inicio"
                                        ></v-time-picker>
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

                                @click="createHorario"
                            >
                                Guardar
                            </v-btn>
                            <v-btn
                                v-else
                                color="primary"

                                @click="updateHorario"
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
            start: null,
        end: null,
            dialog: false,
            update: false,
            horario: {},
            horarios: [],
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
                { text: "Inicio", value: "Inicio" },
                { text: "Fin", value: "Fin" },
                { text: "Estado", value: "estado" },
                { text: "Acciones", sortable: false }
            ]
        };
    },
    created() {

        this.axios.get("/api/horarios/").then(response => {
            this.horarios = response.data;
            this.loading = false;

        });
    },
    methods: {
        createHorario() {
            this.loading = true;
            console.log(this.horario);
             this.axios
                .post("/api/horarios", this.horario)
                .then(() => {
                    this.dialog = false;
                    this.axios.get("/api/horarios/").then(response => {
                        this.horarios = response.data;
                        this.loading = false;

                    });
                })
                .catch(err => console.log(err))
                .finally(() => (this.loading = false));
        },
        addHorario() {
            this.titleForm = "Nuevo Horario";
            this.horario = {};
            this.update = false;
            this.dialog = true;
        },
        editHorario(el) {
            this.titleForm = "Editar Horario";
            this.update = true;
            this.horario.id = el.id;
            this.horario.nombre = el.nombre;
            this.horario.descripcion = el.descripcion;
            this.horario.inicio = el.inicio;
            this.horario.fin = el.fin;
            this.dialog = true;
        },
        updateHorario() {
            this.loading = true;
            this.axios
                .patch(`/api/horarios/${this.horario.id}`, this.horario)
                .then(res => {
                    this.dialog = false;
                    this.axios.get("/api/horarios/").then(response => {
                        this.horarios = response.data;
                        this.loading = false;
                    });
                });
        },
        deleteHorario(el) {
            this.loading = true;
            this.axios.delete(`/api/horarios/${el.id}`).then(() => {
                let i = this.areas.map(data => data.id).indexOf(el.id);
                this.horarios.splice(i, 1);
                this.loading = false;
            });
        }
    }
};
</script>
