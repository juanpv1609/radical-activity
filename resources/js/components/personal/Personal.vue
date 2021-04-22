<template>
    <div>
        <v-card elevation="2" :loading="loading">
            <v-card-title class="d-flex justify-space-between mb-6">

                    Personal Registrado
                <v-btn
                    class="mx-2"
                    fab
                    dark
                    small
                    color="primary"
                    link :to="'/personal-new'"
                >
                    <v-icon dark>
                        mdi-plus
                    </v-icon>
                </v-btn>
            </v-card-title>

            <v-card-text>
                <v-text-field
                    v-model="search"
                    append-icon="mdi-magnify"
                    label="Search"
                    single-line
                    hide-details
                ></v-text-field>
                 <v-data-table
                    :headers="headers"
                    :items="personas"
                    :search="search"
                >
                    <template v-slot:item="row">
                        <tr>
                            <td>{{ row.item.ci }}</td>
                            <td>{{ row.item.nombre }}</td>
                            <td>{{ row.item.apellido }}</td>

                            <td>{{ row.item.email }}</td>
                            <td>{{ row.item.telefono }}</td>
                            <td>{{ row.item.fecha_nacimiento }}</td>
                            <td>
                                <v-btn
                                    icon
                                    color="primary"
                                >
                                    <v-icon dark>mdi-school</v-icon>
                                </v-btn>
                            </td>
                            <td>
                                <v-btn
                                    icon
                                    color="black"
                                >
                                    <v-icon dark>mdi-pencil</v-icon>
                                </v-btn>
                                <v-btn
                                    :disabled="row.item.role == 2"
                                    icon
                                    color="error"
                                >
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
            personas: [],
            dialog: false,
            update: false,
            persona: {},
            loading: true,
            titleForm: null,
            search: "",
            valid:true,
            headers: [
                { text: "CI", value: "ci" },
                { text: "Nombre", value: "nombre" },
                { text: "Apellido", value: "apellido" },
                { text: "Email", value: "email" },
                { text: "Telefono", value: "telefono" },
                { text: "Fecha nacimiento", value: "fecha_nacimiento" },
                { text: "Estudios", value: "estudios" },
                { text: "Acciones", value: "controls", sortable: false }
            ],
        };
    },
    created() {
        this.axios.get("/api/persona").then(response => {
            this.personas = response.data;

            this.loading = false;
        });
    },
    methods: {
        estudios(el) {
                console.log(el);
                this.titleForm = "Editar Usuario";
                this.update = true;
                this.usuario.id = el.id;
                this.usuario.name = el.name;
                this.usuario.email = el.email;
                this.usuario.role = el.role;
                this.usuario.created_at = el.created_at;
                this.dialog = true;
            },
        /*
        editUser(el) {
            console.log(el);
            this.titleForm = "Editar Usuario";
            this.update = true;
            this.persona.id = el.id;
            this.persona.name = el.name;
            this.persona.email = el.email;
            this.persona.role = el.role;
            this.persona.created_at = el.created_at;
            this.dialog = true;
        },
        createUser() {
            this.loading = true;
            this.axios
                .post("/api/usuarios", this.usuario)
                .then(response => {
                    this.dialog = false;
                    this.loading = false;
                    this.axios.get("/api/usuarios-all").then(response => {
                        this.usuarios = response.data;
                        for (const item of this.usuarios) {
                            item.created_at = moment(item.created_at).format(
                                "DD/MM/YYYY hh:mm"
                            );
                        }
                    });
                })
                .catch(err => console.log(err))
                .finally(() => (this.loading = false));
        },
        updateUser() {
            this.loading = true;
            this.axios
                .patch(`/api/usuarios/${this.usuario.id}`, this.usuario)
                .then(res => {
                    this.dialog = false;
                    this.loading = false;
                    this.axios.get("/api/usuarios-all").then(response => {
                        this.usuarios = response.data;
                        for (const item of this.usuarios) {
                            item.created_at = moment(item.created_at).format(
                                "DD/MM/YYYY hh:mm"
                            );
                        }
                    });
                });
        },
        deleteUser(el) {
            this.loading = true;
            this.axios.delete(`/api/usuarios/${el.id}`).then(response => {
                let i = this.usuarios.map(data => data.id).indexOf(el.id);
                this.usuarios.splice(i, 1);
                this.loading = false;
            });
        } */
    }
};
</script>
