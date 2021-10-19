<template>
    <div>
            <v-card elevation="2" :loading="loading">
                <v-card-title class="d-flex justify-space-between mb-6"
                    >
                    <v-badge
                :content="usuarios.length"
                :value="usuarios.length"
                color="green"

            >
            Usuarios

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
                        @click="addUser"
                    >
                        <v-icon dark>
                            mdi-plus
                        </v-icon>
                    </v-btn>
                </v-card-title>

                <v-card-text>
                    <v-data-table
                        :headers="headers"
                        :items="usuarios"
                        :search="search"
                    >
                    <template v-slot:item="row">
                        <tr>
                            <td>{{row.item.name}}</td>
                            <td>{{row.item.email}}</td>
                            <td>{{row.item.rol.nombre}}</td>
                            <td>{{row.item.puesto.descripcion}}</td>
                            <td>{{row.item.created_at}}</td>
                            <td>
                                <v-btn  icon color="primary" @click="editUser(row.item)">
                                    <v-icon dark>mdi-pencil</v-icon>
                                    </v-btn>
                                    <v-btn :disabled="row.item.role==2"  icon color="error" @click="deleteUser(row.item)">
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
                                    <v-row>
                                        <v-col cols="12">
                                            <v-text-field
                                                v-model="usuario.name"
                                                label="Nombre y Apellido*"
                                                required
                                            ></v-text-field>
                                        </v-col>
                                        <v-col cols="12">

                                            <v-text-field
                                                v-model="usuario.email"
                                                label="Direccion de correo*"
                                                required
                                            ></v-text-field>
                                        </v-col>
                                        <v-col cols="12">

                                            <v-select :items="roles"
                                                v-model="role"
                                                label="Rol"
                                                dense
                                                return-object >
                                                <template slot="selection" slot-scope="data">
                                                    <!-- HTML that describe how select should render selected items -->
                                                    {{ data.item.nombre }}
                                                </template>
                                                <template slot="item" slot-scope="data">
                                                    <!-- HTML that describe how select should render items when the select is open -->
                                                    {{ data.item.nombre }}
                                                     </template>
                                            </v-select>
                                        </v-col>
                                        <v-col cols="12">

                                            <v-select :items="perfiles"
                                                v-model="perfil"
                                                label="Perfil Laboral"
                                                dense
                                                return-object >
                                                <template slot="selection" slot-scope="data">
                                                    <!-- HTML that describe how select should render selected items -->
                                                    {{ data.item.descripcion }}
                                                </template>
                                                <template slot="item" slot-scope="data">
                                                    <!-- HTML that describe how select should render items when the select is open -->
                                                    {{ data.item.descripcion }}<v-spacer></v-spacer> <small>({{ data.item.area.nombre }})</small>
                                                     </template>
                                            </v-select>
                                        </v-col>
                                        <v-col cols="12" v-if="!update">
                                            <v-text-field
                                                v-model="usuario.password"
                                                label="Password*"
                                                required
                                                disabled
                                            ></v-text-field>
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

                                    @click="createUser"
                                >
                                    Guardar
                                </v-btn>
                                <v-btn
                                    v-else
                                    color="primary"

                                    @click="updateUser"
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
import moment from 'moment';
    export default {
        data() {
            return {
                usuarios: [],
                dialog: false,
                update: false,
                usuario: {},
                loading: true,
                titleForm: null,
                search: "",

                roles:[
                ],
                role:{},
                perfiles:[
                ],
                perfil:{},
                headers: [
                { text: "Nombre", value: "nombre" },
                { text: "Email", value: "email" },
                { text: "Rol", value: "role" },
                { text: "Perfil Laboral", value: "role" },
                { text: "Fecha creacion", value: "created_at" },
                { text: "Acciones", value: "controls", sortable: false }
            ]

            }
        },
        created() {
            this.initialData();
        },
        methods: {
            initialData(){
                this.axios
                .get('/api/usuarios-all')
                .then(response => {
                    console.log(response.data);
                    this.usuarios = response.data;
                    for (const item of this.usuarios) {
                        item.created_at=moment(item.created_at).format('DD/MM/YYYY hh:mm')
                    }
                    this.loading = false;
                });
                this.axios
                .get('/api/roles')
                .then(response => {
                    this.roles = response.data;

                });
                this.axios
                .get('/api/perfil-puesto')
                .then(response => {
                    console.log(response.data);
                    this.perfiles = response.data;

                });
            },
            addUser() {

                this.titleForm = "Nuevo Usuario";
                this.usuario = {};
                this.usuario.password='R@dical2021';
                this.update = false;
                this.dialog = true;
            },
            editUser(el) {

                this.titleForm = "Editar Usuario";
                this.update = true;
                this.usuario.id = el.id;
                this.usuario.name = el.name;
                this.usuario.email = el.email;
                this.role = el.role;
                this.perfil = el.cargo;
                this.usuario.created_at = el.created_at;
                this.dialog = true;

            },
            createUser() {
                this.loading = true;
                this.usuario.role=this.role.id
                this.usuario.cargo=this.perfil.id
                console.log(this.usuario);
                 this.axios
                    .post('/api/usuarios', this.usuario)
                    .then(response => {
                        this.dialog = false;
                        this.loading = false;
                        this.initialData();
                         })
                    .catch(err => console.log(err))
                    .finally(() => this.loading = false)
            },
            updateUser() {
                this.loading = true;
                this.usuario.role=this.role.id
                this.usuario.cargo=this.perfil.id
                this.axios
                    .patch(`/api/usuarios/${this.usuario.id}`, this.usuario)
                    .then((res) => {
                        this.dialog = false;
                        this.loading = false;
                        this.initialData();
                    });
            },
            deleteUser(el) {
                this.loading = true;
                this.axios
                    .delete(`/api/usuarios/${el.id}`)
                    .then(() => {
                        let i = this.usuarios.map(data => data.id).indexOf(el.id);
                        this.usuarios.splice(i, 1);
                        this.loading = false;
                    });
            }
        }
    }
</script>
