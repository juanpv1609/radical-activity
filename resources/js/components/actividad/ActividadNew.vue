<template>
    <div>
        <v-card elevation="2" :loading="loading">
            <v-card-title class="m-a">
                {{ tipoRegistro }} de actividades - {{ $store.state.user.name }}
                <v-spacer></v-spacer>

                <v-col cols="2">
                    <template>
                        <v-dialog
                            ref="dialog"
                            v-model="menu"
                            :return-value.sync="fechaHoy"
                            :close-on-content-click="true"
                            width="290px"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="fechaHoy"
                                    label="Fecha de registro"
                                    prepend-icon="mdi-calendar"
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                    dense
                                    filed

                                ></v-text-field>
                            </template>
                            <v-date-picker
                                scrollable
                                v-model="fechaHoy"

                                min="1950-01-01"
                                @change="setFecha"
                                :rules="[() => !!fechaHoy || 'This field is required']"
                            >
                            </v-date-picker>
                        </v-dialog>
                    </template>
                </v-col>
                <v-col cols="3">
                    <!-- <v-autocomplete
                        :items="horarios"
                        item-text="nombre"
                        item-value="id"
                        v-model="horario"
                        label="Horario"
                        dense
                        return-object
                        :hint="horario.id ? horario.inicio + ' - ' + horario.fin : 'Seleccione un horario'"
                        persistent-hint
                    ></v-autocomplete> -->
                    <v-select :items="horarios"
                        v-model="horario"
                            label="Horario"
                        dense
                        return-object
                        :hint="horario.id ? horario.nombre + ' ('+horario.inicio + ' - ' + horario.fin+')' : 'Seleccione un horario'"
                        @change="setHorario()"
                        persistent-hint
                        :rules="[() => !!horario || 'This field is required']"
                        >
                        <template slot="selection" slot-scope="data">
                            <!-- HTML that describe how select should render selected items -->
                            {{ data.item.nombre }}
                        </template>
                        <template slot="item" slot-scope="data">
                            <!-- HTML that describe how select should render items when the select is open -->
                            <strong>{{ data.item.nombre }}</strong> <v-spacer></v-spacer> <small>({{ data.item.inicio }} - {{ data.item.fin }})</small>
                        </template>
                    </v-select>
                </v-col>
            </v-card-title>

            <v-card-text>
                <v-row>
                    <v-col cols="12" sm="1">
                        <!-- <vue-timepicker format="HH:mm" :hour-range="hourRange"></vue-timepicker> -->
                        <v-menu
                            ref="inicio"
                            v-model="menu1"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            :return-value.sync="start"
                            transition="scale-transition"
                            offset-y
                            max-width="290px"
                            min-width="290px"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="start"
                                    label="Inicio"
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                    :disabled="!horario.id"
                                ></v-text-field>
                            </template>
                            <v-time-picker
                                v-if="menu1"
                                v-model="start"
                                full-width
                                color="green lighten-1"
                                :min="minimo"
                                :max="maximo"
                                :allowed-hours="(horario.id==3)? allowedHours:null"
                                format="24hr"
                                :allowed-minutes="allowedStep"
                                @click:minute="$refs.inicio.save(start)"
                            ></v-time-picker>
                        </v-menu>
                    </v-col>
                    <v-col cols="12" sm="1">
                        <v-menu
                            ref="fin"
                            v-model="menu2"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            :return-value.sync="end"
                            transition="scale-transition"
                            offset-y
                            max-width="290px"
                            min-width="290px"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="end"
                                    label="Fin"
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                    :disabled="!horario.id"
                                ></v-text-field>
                            </template>
                            <v-time-picker
                                v-if="menu2"
                                v-model="end"
                                full-width
                                :min="minimo"
                                :max="maximo"
                                format="24hr"
                                color="red"
                                :allowed-hours="(horario.id==3)? allowedHours:null"
                                :allowed-minutes="allowedStep"
                                @click:minute="$refs.fin.save(end)"

                            ></v-time-picker>
                        </v-menu>
                    </v-col>
                    <v-col cols="12" sm="2">
                        <v-autocomplete
                                :items="customers"
                                item-text="Name"
                                item-value="Id"
                                v-model="customer"
                                label="Cliente"
                                return-object
                                small
                                :disabled="(!start || !end)"
                            ></v-autocomplete>
                    </v-col>
                    <v-col cols="12" sm="2">
                        <v-autocomplete
                            :items="clasificaciones"
                            item-text="nombre"
                            item-value="id"
                            v-model="clasificacion"
                            label="Clasificación"
                            return-object
                            :disabled="!customer.Name"
                        ></v-autocomplete>
                    </v-col>
                    <!-- <v-col cols="12" sm="2">
                        <v-autocomplete
                            :items="tipoActividades"
                            item-text="descripcion"
                            item-value="id"
                            v-model="tipoActividad"
                            label="Tipo de Actividad"
                            return-object
                            :disabled="(!start || !end)"
                        ></v-autocomplete>
                    </v-col> -->
                    <v-col cols="12" sm="5">
                        <v-combobox
                            v-model="modelDescripcion"
                            :items="itemsDescripcion"
                            hide-selected
                            hint="Ingrese la descripción de la actividad y presione Enter, TAB o 'Punto y coma'"
                            label="Actividades"
                            multiple
                            persistent-hint
                            small-chips
                            deletable-chips
                            :delimiters="[';']"
                            @change="delimitActividades"
                            :disabled="!clasificacion.id"
                        >
                        </v-combobox>
                    </v-col>
                    <v-col cols="12" sm="1">
                        <v-btn
                            small
                            color="orange accent-4"
                            block
                            large
                            @click="controlFechas"
                            title="Agregar actividades a la lista"
                            :disabled="(modelDescripcion.length == 0)"
                        >
                            <v-icon>mdi-plus-thick</v-icon>
                        </v-btn>
                    </v-col>
                </v-row>
                <v-row v-if="ActivityLine.length > 0">
                    <v-col cols="12">
                        <v-data-table :headers="headers" :items="ActivityLine"  class="elevation-2">
                            <template v-slot:item="row">
                                <tr>
                                    <!-- <td>{{ row.item.fecha }}</td> -->
                                    <td>
                                        {{ row.item.h_inicio }} - {{ row.item.h_fin }}
                                    </td>
                                    <td>
                                        {{ row.item.customer }}
                                    </td>
                                    <td>
                                        {{ row.item.clasificacion_nombre }}
                                    </td>
                                    <td>
                                        <v-chip
                                            v-for="actividad in row.item
                                                .descripcion"
                                            :key="actividad"
                                            small
                                            label
                                            class="ma-1"
                                        >
                                            {{ actividad }}
                                        </v-chip>
                                    </td>
                                    <td>
                                        <v-edit-dialog
                                            :return-value.sync="
                                                row.item.colaboradores
                                            "
                                            large
                                            persistent
                                            @click:save="
                                                saveColaborador(row.item)
                                            "
                                            @cancel="cancelColaborador"
                                            @open="openColaborador"
                                            @close="closeColaborador"
                                        >
                                            <div>
                                                <v-chip
                                                    v-for="colaborador in row
                                                        .item.colaboradores"
                                                    :key="colaborador"
                                                    class="ma-1"
                                                    x-small
                                                    color="primary"
                                                    pill
                                                >
                                                    {{ colaborador }}
                                                </v-chip>
                                            </div>
                                            <template v-slot:input>
                                                <div class="mt-4 title">
                                                    Colaboradores
                                                </div>
                                                <v-combobox
                                                    v-model="
                                                        row.item.colaboradores
                                                    "
                                                    :items="itemsColaboradores"
                                                    hide-selected
                                                    hint="Ingrese el nombre del colaborador y presione TAB "
                                                    label="Colaboradores"
                                                    multiple
                                                    persistent-hint
                                                    small-chips
                                                    deletable-chips
                                                >
                                                </v-combobox>
                                            </template>
                                        </v-edit-dialog>
                                    </td>
                                    <td>
                                        <v-edit-dialog
                                            :return-value.sync="
                                                row.item.observacion
                                            "
                                            large
                                            persistent
                                            @save="save(row.item)"
                                            @cancel="cancel"
                                            @open="open"
                                            @close="close"
                                        >
                                            <div>
                                                {{ row.item.observacion }}
                                            </div>
                                            <template v-slot:input>
                                                <div class="mt-4 title">
                                                    Observación / Resultados
                                                </div>
                                                <v-textarea
                                                    v-model="
                                                        row.item.observacion
                                                    "
                                                    label="Observación / Resultados"
                                                    clear-icon="mdi-close-circle"
                                                    clearable
                                                    autofocus
                                                    rows="2"
                                                ></v-textarea>
                                            </template>
                                        </v-edit-dialog>
                                    </td>

                                    <td >
                                        <v-checkbox
                                            v-model="row.item.estado"
                                            color="success"
                                            dense
                                        ></v-checkbox>
                                    </td>
                                    <td>
                                        <v-btn
                                            icon
                                            color="error"
                                            x-small
                                            @click="deleteActivityLine(row.item)"
                                        >
                                            <v-icon dark>mdi-delete</v-icon>
                                        </v-btn>
                                    </td>
                                </tr>
                            </template>
                        </v-data-table>
                        <v-snackbar
                            v-model="snack"
                            :timeout="3000"
                            :color="snackColor"
                        >
                            {{ snackText }}

                            <template v-slot:action="{ attrs }">
                                <v-btn
                                    v-bind="attrs"
                                    text
                                    @click="snack = false"
                                >
                                    Cerrar
                                </v-btn>
                            </template>
                        </v-snackbar>
                    </v-col>
                </v-row>
                <!-- <v-row v-if="ActivityLine.length > 0">
                    <v-col cols="12" sm="3">
                        <v-switch
                            v-model="enviar"
                            color="orange"
                            :label="
                                `${
                                    enviar
                                        ? 'Enviar correo'
                                        : 'NO enviar correo'
                                }`
                            "
                        ></v-switch>
                    </v-col>
                    <v-col cols="12" sm="9">
                        <v-combobox
                            v-model="modelDestinatarios"
                            :items="itemsDestinatarios"
                            hide-selected
                            hint="Ingrese el correo electrónico del destinatario y presione ENTER o TAB para agregar"
                            label="Destinatarios"
                            multiple
                            persistent-hint
                            small-chips
                            deletable-chips
                            :disabled="!enviar"
                            suffix="@gruporadical.com"
                        >
                        </v-combobox>
                    </v-col>
                </v-row> -->
            </v-card-text>

            <v-card-actions v-if="ActivityLine.length > 0">
                <v-spacer></v-spacer>
                <v-btn block color="primary" large @click="verificarFechaActividades"
                    >enviar {{ActivityLine.length}} actividades</v-btn
                >
            </v-card-actions>
        </v-card>
    </div>
</template>

<script>
import moment from 'moment'
export default {
    data() {
        return {
            start: null,
            end: null,
            menu: false,
            menu1: false,
            menu2: false,
            fechaHoy: null,
            minimo:null,
            maximo:null,

            enviar: false,
            //////
            itemsDescripcion: [],
            modelDescripcion: [],
            modelColaboradores: [],
            itemsColaboradores: [],
            modelDestinatarios: [],
            itemsDestinatarios: ['doris.gonzalez','claudio.cortes','cristina.jimenez'],
            snack: false,
            snackColor: "",
            snackText: "",
            /////
            descripcion: null,
            horarios: [],
            tipoActividades: [],
            tipoActividad: {},
            horario: {},
            persona_estudios: [],
            dialogEstudios: false,
            update: false,
            persona: {},
            loading: false,
            titleFormEstudios: null,
            search: "",
            searchEstudios: "",
            valid: true,
            headers: [
                //{ text: "Fecha", value: "fecha" },
                { text: "Inicio - Fin", value: "inicio" },
                { text: "Cliente", value: "customer", sortable: false },
                { text: "Clasificación", value: "clasificacion_nombre", sortable: false },
                //{ text: "Tipo", value: "tipo", sortable: false },
                { text: "Actividades", value: "descripcion", sortable: false },
                { text: "Colaboradores", value: "colaborador", sortable: false },
                { text: "Observación / Resultados", value: "observacion", sortable: false },
                { text: "Completada", value: "estado", sortable: false },
                { text: "Eliminar", value: "controls", sortable: false }
            ],
            foto: null,
            ActivityLine: [],
            Activity: {},
            hourRange:[],
            customers:[],
            customer:{},
            clasificacion: {},
            clasificaciones: [],
        };
    },
    computed:{
        tipoRegistro(){
            var f = new Date();
            return (this.fechaHoy<=f.toISOString().substr(0, 10)) ? 'Registro' : 'Planificación'
        }
    },
    watch: {
        modelDescripcion(val) {
            if (val.length > 10) {
                this.$nextTick(() => this.modelDescripcion.pop());
            }
        }
    },
    created() {
        this.initialData();
        var f = new Date();
        this.fechaHoy = f.toISOString().substr(0, 10);
    },
    methods: {
        save(el) {
            this.ActivityLine.observacion = el.observacion;
        },
        cancel() {
            this.snack = true;
            this.snackColor = "error";
            this.snackText = "Cancelado";
        },
        open() {
            this.snack = true;
            this.snackColor = "info";
            this.snackText = "Editar Observación / Resultados";
        },
        close() {
            console.log("Dialog closed");
        },
        saveColaborador(el) {
            this.ActivityLine.colaboradores = el.colaboradores;
        },
        cancelColaborador() {
            this.snack = true;
            this.snackColor = "error";
            this.snackText = "Cancelado";
        },
        openColaborador() {
            this.snack = true;
            this.snackColor = "info";
            this.snackText = "Editar Observación / Resultados";
        },
        closeColaborador() {
            console.log("Dialog closed");
        },
        allowedStep: m => m % 5 === 0,
        initialData() {
            //var obj = {Id:'fdsff-fsdfsf-fsfdsftr-gfgfd',Name:'OTRO (NO DEFINIDO)'};
                    //this.customers.push(obj)
            this.axios
                .get('/api/customers/')
                .then(response => {
                    this.customers = response.data;
                });
                this.axios.get("/api/clasificacion/").then(response => {
                this.clasificaciones = response.data;

                });
                    //console.log(this.customers);
            this.axios.get(`/api/horarios`).then(response => {
                this.horarios = response.data;
                //console.log(this.horarios);
            });
            this.axios.get(`/api/tipo-actividad`).then(response => {
                this.tipoActividades = response.data;
            });
            this.start=null;
            this.end=null;
        },
        setFecha(fechaHoy) {
            this.$refs.dialog.save(fechaHoy);
        },
        allowedHours: v => !(v >= 7 && v <= 21),
        setHorario(){
            console.log(this.horario)
             if (this.horario.id==3) {
            this.minimo=null;
            this.maximo=null;
            this.start='22:00';

            }else{
            this.minimo=this.horario.inicio.substring(0, 5);
            this.start=this.minimo.substring(0, 5);
            this.maximo=this.horario.fin.substring(0, 5);
            }

        },
        controlFechas(){
            if (this.horario.id==3) { //horario nosturno 22:00 - 06:00
                this.addActivityLine();
            }else{
                if (this.start>=this.end) {
                this.$swal.fire({
                                title: 'Incorrecto!',
                                text: `La hora final debe ser mayor a la inicial`,
                                icon: 'error',
                                });
                }else{
                    this.addActivityLine();
                }
            }
        },
        addActivityLine() {

                    var activityLine = {};
                    this.ActivityLine = this.ActivityLine || [];
                    console.log(this.modelDescripcion);
                    activityLine.customer = this.customer.Name;
                    activityLine.clasificacion = this.clasificacion.id;
                    activityLine.clasificacion_nombre = this.clasificacion.nombre;
                    activityLine.tipo_actividad = this.tipoActividad.id;
                    activityLine.tipo_actividad_nombre = this.tipoActividad.descripcion;
                    activityLine.descripcion = this.modelDescripcion;
                    activityLine.observacion = null;
                    activityLine.colaboradores = this.modelColaboradores;
                    activityLine.h_inicio = this.start;
                    activityLine.h_fin = this.end;
                    activityLine.estado = true;
                    console.log(activityLine);
                    this.ActivityLine.push(activityLine);
                    this.start = this.end;
                    //this.tipoActividad.id=null;
                    this.end = null;
                    this.descripcion = null;
                    this.customer={}
                    this.clasificacion={}
                    this.modelDescripcion = [];



        },
        deleteActivityLine(el) {
            let i = this.ActivityLine.map(data => data.h_inicio).indexOf(
                el.h_inicio
            );
            this.ActivityLine.splice(i, 1);
        },
        verificarFechaActividades(){

            this.axios.get(`/api/verificar-actividades/${this.fechaHoy}/${this.$store.state.user.id}`).then(res => {
                if (res.data.length>0) { //verifica si tiene actividades registradas con la misma fecha
                    this.$swal.fire({
                                title: 'Error',
                                html: `Estimado usuario ya tiene actividades registradas con fecha ${this.fechaHoy}, procure modificar la actividad creada anteriormente`,
                                icon: 'error'
                                })
                } else {
                    this.enviarActividades()
                }
            }).catch((err)=> {
                    this.$swal.fire({
                                title: 'Error',
                                html: `${err}`,
                                icon: 'error'
                                })
                }).finally();
        },
        enviarActividades() {
            //this.loading = true;
            var f = new Date();
            this.Activity.planificada = (this.fechaHoy<=f.toISOString().substr(0, 10)) ? 0 : 1
            this.Activity.usuario = this.$store.state.user.id;
            this.Activity.horario = this.horario.id;
            this.Activity.fecha = this.fechaHoy;
            //this.Activity.enviaMail = this.enviar;
            this.Activity.destinatarios = this.modelDestinatarios;
            //this.Activity.colaboradores = this.modelColaboradores;
            this.Activity.activities = this.ActivityLine;

            console.log(this.ActivityLine);
            var min = this.ActivityLine[0].h_inicio;
            var max = this.ActivityLine[0].h_fin;
            var diff = 0.0;
            var diff_tiempo_libre = 0.0;
            if (this.ActivityLine.length>1) {
                for (var i = 1; i < this.ActivityLine.length; i++) {
                        if (this.ActivityLine[i].h_fin > max) {
                            max = this.ActivityLine[i].h_fin;
                        }
                        if (this.ActivityLine[i].h_inicio < min) {
                            min = this.ActivityLine[i].h_inicio;
                        }
                        diff = moment.duration(moment(max,'HH:mm').diff(moment(min,'HH:mm'))).asHours();

                    if (this.ActivityLine[i].clasificacion == 14 ) { // tipo break
                        diff_tiempo_libre = moment.duration(moment(this.ActivityLine[i].h_fin,'HH:mm').diff(moment(this.ActivityLine[i].h_inicio,'HH:mm'))).asHours();
                    }


                }
            } else {
                 min = this.ActivityLine[0].h_inicio;
                 max = this.ActivityLine[0].h_fin;
                diff = moment.duration(moment(max,'HH:mm').diff(moment(min,'HH:mm'))).asHours();

            }
            if (this.horario.id==3) { //si es horario N1 NOCTURNO 22:00 - 06:00

                    if (this.ActivityLine[0].h_inicio=='22:00') {
                        min = '00:00';
                        max = '08:00';
                    }
                    diff = moment.duration(moment(max,'HH:mm').diff(moment(min,'HH:mm'))).asHours();
                }
            this.Activity.horas_p = diff.toFixed(2);
            this.Activity.horas_np = diff_tiempo_libre.toFixed(2);
            this.Activity.horas_total = (diff-diff_tiempo_libre).toFixed(2)
            console.log('mayor: '+max);
            console.log('menor: '+min);
            console.log('diff: '+diff.toFixed(2));
            console.log('diff tiempo libre: '+diff_tiempo_libre.toFixed(2));
            var tiempo_libre=0;
            if (this.horario.id<=3) { //si es N1
                tiempo_libre=1;
                } else {
                    this.Activity.activities.forEach(element => {
                    //actividade tipo break / almuerzo

                        tiempo_libre+=(element.clasificacion==14) ? true: false;

                    });
                }

            console.log(tiempo_libre);
            // if (tiempo_libre==0 ) {
            //     this.$swal.fire({
            //                     title: 'Atención!',
            //                     text: `No olvide registrar al menos una actividad de tipo "Break" o tiempo libre`,
            //                     icon: 'warning',
            //                     });
            // } else {
                this.$swal
                .fire({
                    title: "Esta seguro?",
                    html: `Estimado ${this.$store.state.user.name} a continuación registrará <strong>${this.Activity.activities.length}</strong>
                    actividades correspondientes al <strong>${this.Activity.fecha}</strong> <br>
                    Con un total de <strong>${(diff-diff_tiempo_libre).toFixed(2)}</strong> horas registradas.`,
                    icon: "question",
                    showConfirmButton: true,
                    showCancelButton: true
                })
                .then(res => {
                    if (res.value) {
                        this.$swal.fire({
                        title: 'Espere',
                        text: 'Registrando actividades...',
                        icon: 'warning',
                        allowOutsideClick: false
                    });
                    this.$swal.showLoading();
                    this.axios
                        .post("/api/actividades", this.Activity)
                        .then(() => {

                            this.$swal.fire({
                                        title: 'Correcto',
                                        text: 'Actividades registradas correctamente!',
                                        icon: 'success',
                                        timer: 1500,
                                        timerProgressBar: true,
                                        });

                        })
                        .catch(err => {
                            this.$swal.fire({
                                        title: 'Incorrecto',
                                        text: `Ocurrió un error!\n${err}`,
                                        icon: 'error',
                                        });
                        })
                        .finally(() => {
                            this.Activity = {};
                            this.ActivityLine = [];
                            this.loading=false;
                            this.initialData();
                        });
                    }
                });

            //}

        },
        delimitActividades (v) {
                const reducer = (a, e) => [...a, ...e.split(/[;]+/)]
                this.modelDescripcion = [...new Set(v.reduce(reducer, []))]
                },
    }
};
</script>
