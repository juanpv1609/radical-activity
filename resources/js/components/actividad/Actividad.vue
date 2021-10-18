<template>
    <div>
        <v-card elevation="2" :loading="loading">
            <v-card-title >
                Mi Actividad
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
                    fab
                    color="primary"
                    small
                    :to="{
                        name: 'actividad-new'
                    }"
                >
                    <v-icon>mdi-newspaper-plus</v-icon>
                </v-btn>
            </v-card-title>

            <v-card-text>
                <v-skeleton-loader
                    v-if="firstLoad"
                    :loading="loading"
                    type="table"
                ></v-skeleton-loader>
                <v-data-table
                    :headers="headers"
                    :items="actividades"
                    :search="search"
                    v-show="!firstLoad"
                    sort-by="actividad.fecha"
                    group-by="actividad.fecha"
                    show-group-by
                >
                    <template v-slot:item="row">
                        <tr>
                            <td v-if="headers.text == 'Fecha'">
                                {{ row.item.actividad.fecha }}
                            </td>
                            <td>{{ row.item.h_inicio }}</td>
                            <td>{{ row.item.h_fin }}</td>

                            <td>{{ row.item.descripcion }}</td>

                            <td>
                                {{ row.item.actividad.usuario.name }}
                            </td>

                            <td>{{ row.item.actividad.horario.nombre }}</td>
                            <td>{{ row.item.tipo.descripcion }}</td>
                            <td>
                                <v-chip
                                    x-small
                                    :color="row.item.status.color"
                                    label
                                >
                                    {{ row.item.status.descripcion }}
                                </v-chip>
                            </td>

                           <!--  <td>
                                <v-btn
                                    icon
                                    color="primary"
                                    small
                                    :to="{
                                        name: 'actividad-edit',
                                        params: { id: row.item.id }
                                    }"
                                >
                                    <v-icon>mdi-pencil</v-icon>
                                </v-btn>
                                <v-btn
                                    :disabled="row.item.role == 2"
                                    icon
                                    small
                                    color="error"
                                    @click="deletePerson(row.item)"
                                >
                                    <v-icon dark>mdi-delete</v-icon>
                                </v-btn>
                            </td> -->
                        </tr>
                    </template>
                </v-data-table>
            </v-card-text>

            <v-card-actions> </v-card-actions>
        </v-card>
    </div>
</template>

<script>
import moment from 'moment'

export default {
    data() {
        return {
            firstLoad: true,
            actividades: [],
            persona_estudios: [],
            dialogEstudios: false,
            update: false,
            persona: {},
            loading: true,
            titleFormEstudios: null,
            search: "",
            searchEstudios: "",
            valid: true,
            headers: [
                {
                    text: "Fecha",
                    value: "actividad.fecha",
                    align: "start",
                    groupable: true
                },
                { text: "Inicio", value: "h_inicio", groupable: false },
                { text: "Fin", value: "h_fin", groupable: false },
                { text: "Descripción", value: "descripcion", groupable: false },
                {
                    text: "Usuario",
                    value: "actividad.usuario.name",
                    groupable: false
                },
                {
                    text: "Horario",
                    value: "actividad.horario.nombre",
                    groupable: false
                },
                {
                    text: "Tipo",
                    value: "tipo_actividad.descripcion",
                    groupable: false
                },
                {
                    text: "Estado",
                    value: "status.descripcion",
                    groupable: false,
                    align: "start"
                },
                /* {
                    text: "Acciones",
                    value: "controls",
                    sortable: false,
                    groupable: false,
                    align: "start"
                } */
            ]
        };
    },
    created() {
        const query =
            ((this.$store.state.user.role == 2)//ADMIN
            || this.$store.state.user.role == 3) //SUPERVISOR
                ? `actividades`
                : `actividades/${this.$store.state.user.id}`;
        this.axios.get(`/api/${query}`).then(response => {
            this.actividades = response.data;
            console.log(this.actividades);
        });
        this.loading = false;
        this.firstLoad = false;
    },
    computed:{
        formatTime(value){
            if (value) {
                return moment(String(value)).format('hh:mm')
            }
        },
    },
    methods: {

        estudios(el) {
            console.log(el);
            this.loading = true;
            this.titleFormEstudios = `${el.nombre} ${el.apellido}`;
            this.foto = !(el.foto == null || el.foto == "")
                ? "data:image/png;base64," + el.foto
                : null;
            //this.usuario.id = el.id;
            this.axios.get(`/api/persona-estudios/${el.id}`).then(response => {
                //this.personas = response.data;

                this.persona_estudios = response.data;
                console.log(this.persona_estudios);
                this.dialogEstudios = true;
                this.loading = false;
            });
        },
        viewDocuments(el) {
            //console.log(el.documentos);
            var arrayDocumentos = el.documentos.split(",");
            console.log(arrayDocumentos);
            arrayDocumentos.forEach(element => {
                window.open("data:image/png;base64," + element, "_blank");
            });
            //this.logo = (!(el.logo==null || el.logo=='')) ? "data:image/png;base64," + el.logo : null;
        },
        deletePerson(el) {
            this.loading = true;
            this.axios.delete(`/api/persona/${el.id}`).then(response => {
                let i = this.personas.map(data => data.id).indexOf(el.id);
                this.personas.splice(i, 1);
                this.loading = false;
            });
        }
    }
};
</script>
