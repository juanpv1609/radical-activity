<template>
    <div>
        <v-card elevation="2" >
            <v-card-title >
                <v-btn-toggle
                    v-model="icon"
                    borderless
                    dense
                    active-class="primary--text"

                >
                    <v-btn value="left" :to="{ name: 'mi-actividad'}">
                    <span class="hidden-sm-and-down">Lista</span>

                    <v-icon right>
                        mdi-format-align-left
                    </v-icon>
                    </v-btn>

                    <v-btn value="center" :to="{ name: 'mi-actividad-calendar'}">
                    <span class="hidden-sm-and-down">Calendario</span>

                    <v-icon right>
                        mdi-calendar
                    </v-icon>
                    </v-btn>

                </v-btn-toggle>

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
                <v-btn-toggle


                    >
                <v-btn

                    color="primary"
                    small
                    dark
                    :to="{
                        name: 'actividad-new'
                    }"
                >
                    <v-icon color="white">mdi-plus-thick</v-icon> NUEVO REGISTRO
                </v-btn>
                <v-btn
                    v-show="selected.length>0"
                    color="green"
                    small
                    dark
                    @click="exportData"
                >
                    <v-icon color="white">mdi-microsoft-excel</v-icon> EXPORTAR
                </v-btn>
                </v-btn-toggle>
            </v-card-title>

            <v-card-text>

                <v-data-table
                    :headers="headers"
                    :items="actividades"
                    :search="search"
                    class="elevation-2"
                    :loading="loading"
                    loading-text="Loading... Please wait"
                    item-key="id"
                    show-select
                    v-model="selected"
                    :footer-props="{
                        showFirstLastPage: true,
                        firstIcon: 'mdi-arrow-collapse-left',
                        lastIcon: 'mdi-arrow-collapse-right',
                        prevIcon: 'mdi-minus',
                        nextIcon: 'mdi-plus'
                        }"
                >
                    <template v-slot:item="row">
                        <tr>
                            <td >
                                <v-checkbox
                                :input-value="row.isSelected"
                                @change="row.select($event)"
                                hide-details

                                ></v-checkbox>
                            </td>
                            <td >
                                {{ row.item.fecha }}
                            </td>
                            <!-- <td>{{ row.item.h_inicio }}</td>
                            <td>{{ row.item.h_fin }}</td>

                            <td>{{ row.item.descripcion }}</td> -->

                            <td>
                                {{ row.item.usuario.name }}
                            </td>
                            <td>
                                {{ row.item.usuario.puesto.area.nombre }}
                            </td>
                            <td>
                                {{ row.item.usuario.puesto.descripcion }}
                            </td>
                            <!-- <td>{{ row.item.tipo.descripcion }}</td>
                            <td>
                                <v-chip
                                    x-small
                                    :color="row.item.status.color"
                                    label
                                >
                                    {{ row.item.status.descripcion }}
                                </v-chip>
                            </td> -->
                            <th>
                                     <v-chip
                                    small
                                    color="green"
                                    dark label
                                >
                                    {{ row.item.actividades_count }}
                                     </v-chip>
                                 </th>
                                  <td>{{ row.item.hora_inicio }} </td>
                            <td>{{ row.item.hora_fin }} </td>
                                  <td>
                                      <strong class="primary--text">{{ row.item.horas_total }}</strong>

                                 </td>
                             <td>

                                 <v-btn
                                    icon
                                    color="primary"
                                    small
                                    title="Ver actividades"
                                    @click="findActividades(row.item)"

                                >
                                    <v-icon>mdi-eye-outline</v-icon>
                                </v-btn>
                                <v-btn
                                v-if="$store.state.user.role==2"
                                    icon
                                    color="orange"
                                    small
                                    link
                                    title="Editar actividades"
                                    :to="{
                                        name: 'actividad-edit',
                                        params: { id: row.item.id }
                                    }"
                                >
                                    <v-icon>mdi-pencil</v-icon>
                                </v-btn>
                                <v-btn v-else
                                :disabled="$store.state.user.id!=row.item.usuario.id"
                                    icon
                                    color="orange"
                                    small
                                    link
                                    title="Editar actividades"
                                    :to="{
                                        name: 'actividad-edit',
                                        params: { id: row.item.id }
                                    }"
                                >
                                    <v-icon>mdi-pencil</v-icon>
                                </v-btn>


                                <v-btn
                                    :disabled="$store.state.user.id!=row.item.usuario.id"
                                    icon
                                    small
                                    title="Eliminar actividad"
                                    color="error"
                                    @click="deleteActivity(row.item)"
                                >
                                    <v-icon >mdi-delete</v-icon>
                                </v-btn>
                            </td>
                        </tr>
                    </template>
                </v-data-table>
            </v-card-text>

            <v-card-actions> </v-card-actions>
        </v-card>
        <template>
        <div
            class="modal fade"
            id="exampleModal2"
            tabindex="-1"
            role="dialog"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
        >
            <div
                class="modal-dialog modal-xl modal-dialog-scrollable"
                role="document"
            >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            {{title}}
                        </h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <v-data-table :headers="headersDetalle"
                                        :items="detalleActividades"
                                        >


                            <template v-slot:item="{item}" >
                                <tr  >
                                    <td>
                                        {{ item.h_inicio }}
                                    </td>
                                    <td>
                                        {{ item.h_fin }}
                                    </td>
                                    <td>
                                        {{ (item.cliente) ? item.cliente : null }}
                                    </td>
                                    <td>
                                        {{ (item.clasif) ? item.clasif.nombre : null }}
                                    </td>
                                    <td>
                                        {{ item.descripcion }}
                                    </td>
                                    <td>
                                        <v-chip
                                            x-small
                                            :color="item.status.color"
                                            label
                                            dark
                                        >
                                            {{ item.status.descripcion }}
                                        </v-chip>
                                    </td>
                                </tr>
                            </template>
                       </v-data-table>
                         </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-between">
                            <v-btn
                            color="error"
                            text
                            data-dismiss="modal"
                        >
                            Cerrar
                        </v-btn>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
    </div>
</template>

<script>
import moment from 'moment'
import XLSX from 'xlsx/xlsx.js';
export default {
    data() {
        return {
            selected: [],
            firstLoad: true,
            icon: 'justify',
            actividades: [],
            detalleActividades: [],
            update: false,
            persona: {},
            loading: true,
            search: "",
            valid: true,
            title:"",
            headers: [
                {
                    text: "Fecha",
                    value: "fecha"
                },
               /*  { text: "Inicio", value: "h_inicio", groupable: false },
                { text: "Fin", value: "h_fin", groupable: false },
                { text: "Descripción", value: "descripcion", groupable: false }, */
                {
                    text: "Usuario",
                    value: "usuario.name"
                },
                {
                    text: "Area",
                    value: "usuario.puesto.area.nombre"
                },
                {
                    text: "Cargo",
                    value: "usuario.puesto.descripcion"
                },

                {
                    text: "Actividades",
                    value: "actividades",
                    filterable: false,
                    align: "start"
                },
                {
                    text: "Inicio",
                    value: "hora_inicio",
                    sortable: false,
                    filterable: false,
                },
                {
                    text: "Fin",
                    value: "hora_fin",
                    sortable: false,
                    filterable: false,
                },
                {
                    text: "Horas",
                    value: "horas_total",
                    filterable: false,
                    align: "start"
                },
                /* {
                    text: "Tipo",
                    value: "tipo_actividad.descripcion",
                    groupable: false
                },
                {
                    text: "Estado",
                    value: "status.descripcion",
                    groupable: false,
                    align: "start"
                }, */
                 {
                    text: "Acciones",
                    value: "controls",
                    sortable: false,
                    filterable: false,
                    align: "start"
                }
            ],
            headersDetalle:[
                { text: "Inicio", value: "h_inicio", },
                { text: "Fin", value: "h_fin", },
                { text: "Cliente", value: "cliente", },
                 {
                    text: "Clasificación",
                    value: "clasif.nombre",
                                    },
                { text: "Descripción", value: "descripcion", },
                {
                    text: "Estado",
                    value: "status.descripcion",

                    align: "start"
                },

            ]
        };
    },
    created() {
        const query = `mi-actividad/${this.$store.state.user.id}`;
        this.axios.get(`/api/${query}`).then(response => {
            this.actividades = response.data;
            console.log(this.actividades);
        this.loading = false;
        }).catch((err)=> {
                    this.$swal.fire({
                                title: 'Error',
                                html: `${err}`,
                                icon: 'error'
                                })
                }).finally();
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
        findActividades(el) {
            console.log(el);
             this.axios.get(`/api/detalle-actividades/${el.id}`).then(res => {
                //this.contratos = res.data;
                console.log(res.data);
                this.detalleActividades = res.data;
                this.title = 'Usuario: '+el.usuario.name+' | Fecha: '+el.fecha;
                $("#exampleModal2").modal("show");
                //this.dialogTareas=true;
            }).catch((err)=> {
                    this.$swal.fire({
                                title: 'Error',
                                html: `${err}`,
                                icon: 'error'
                                })
                }).finally();
        },
        exportData(){
            //var data = [];
            console.log(this.actividades);
            var data = this.actividades.map((item) => {
                        return {
                            id: item.id,
                            usuario: item.usuario.name,
                            area: item.usuario.puesto.area.nombre,
                            cargo: item.usuario.puesto.descripcion,
                           // cliente: item.cliente,
                            fecha: item.fecha,
                            hora_inicio: item.hora_inicio,
                            hora_fin: item.hora_fin,
                            horas_p: item.horas_p,
                            horas_np: item.horas_np,
                            horas_total: item.horas_total,
                            horario: item.horario.nombre,
                            actividades: item.actividades_count,
                            created_at: item.created_at,
                        }
                    });
            /* this line is only needed if you are not adding a script tag reference */
            //if(typeof XLSX == 'undefined') XLSX = require('xlsx/xlsx.js');

            /* make the worksheet */
            var ws = XLSX.utils.json_to_sheet(data);

            /* add to workbook */
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Actividades");

            /* generate an XLSX file */
            XLSX.writeFile(wb, "sheetjs.xlsx");
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
        deleteActivity(el) {
            console.log(el);
             this.loading = true;
            this.$swal.fire({
                title: 'Esta seguro?',
                html: `Se eliminará definitivamente las actividades del usuario <strong>${el.usuario.name}</strong> pertenecientes al ${el.fecha}`,
                icon: 'question',
                showConfirmButton: true,
                showCancelButton: true

                }).then(res => {
                if (res.value) {
                    this.axios.delete(`/api/actividades/${el.id}`).then(() => {

                        this.$swal.fire({
                            title: 'Correcto',
                                text: 'Se eliminó la actividad correctamente!',
                                icon: 'success'
                                })
                        let i = this.actividades.map(data => data.id).indexOf(el.id);
                        this.actividades.splice(i, 1);
                }).catch((err)=> {
                    this.$swal.fire({
                                title: 'Error',
                                text: `NO se pudo eliminar la actividad correctamente!\n${err}`,
                                icon: 'error'
                                })
                }).finally(()=>this.loading = false);

                }
                this.loading = false
                })

        }
    }
};
</script>
