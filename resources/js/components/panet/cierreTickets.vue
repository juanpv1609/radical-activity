<template>

    <div>
        <v-card elevation="2" :loading="loading">
            <v-card-title
          >

            Cierre de tickets - ProactivaNET  <v-btn color="primary" icon  @click="helpFile" >
                                    <v-icon >mdi-help-circle </v-icon>
                                </v-btn>
          <v-spacer></v-spacer>
           <v-col cols="auto">
              <v-btn
                    class="mx-2"
                    block
                    elevation="2"
                    x-large
                    color="primary"
                    @click="verificarTickets"
                    :disabled="(ticketsFile.length==0)"
                >
                    TICKETS ANALIZADOS: {{ (tickets.length>0) ? tickets.length+'/'+ticketsFile.length+' TICKETS' : '' }}
                </v-btn>
          </v-col>

        </v-card-title>

            <v-card-text>
                    <v-form
                        ref="form"
                        v-model="valid"
                        lazy-validation
                    >
                    <v-row>
                        <v-col cols="12" sm="3">
                             <v-autocomplete
                                :items="customers"
                                item-text="Name"
                                item-value="Id"
                                v-model="customer"
                                label="Seleccione un cliente"
                                return-object
                                outlined
                            ></v-autocomplete>
                        </v-col>
                        <v-col cols="12" sm="3">
                             <v-autocomplete
                                :items="types"
                                item-text="Name"
                                item-value="Id"
                                v-model="type"
                                label="Seleccione un tipo de ticket"
                                return-object
                                outlined
                            ></v-autocomplete>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-file-input
                            show-size

                            outlined
                            accept=".xls,.xlsx,.ods"
                            v-model="file"
                            label="Seleccione un archivo (.xls, .xlsx)"
                            @change="onChangeFile"
                            :rules="rules.file"
                            hint="El archivo deberá tener una sola hoja y dos columnas (ticket, comentario)"
                                persistent-hint
                                :disabled="Object.keys(customer).length === 0"
                            >

                        </v-file-input>
                        </v-col>



                    </v-row>
                    </v-form>
                    <v-row dense>
                        <v-col >
                            <v-data-table
                                    :headers="headers"
                                    :items="tickets"
                                    :search="search"
                                    class="elevation-2"
                                    dense
                                    v-if="tickets.length>0"
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
                                        <td>{{row.item.code}}</td>
                                       <td>
                                            <v-chip small dark
                                            :color="(row.item.status) ? 'red' : 'green'">
                                                {{(row.item.status) ? 'Cerrado' : 'Nuevo'}}
                                            </v-chip>
                                            </td>
                                        <td>{{row.item.comment}}</td>
                                        <!-- <td>
                                                <v-btn  icon color="error" @click="deleteItem(row.item)">
                                                <v-icon dark>mdi-delete</v-icon>
                                            </v-btn>
                                        </td> -->
                                    </tr>
                                </template>
                                </v-data-table>
                        </v-col>
                    </v-row>

            </v-card-text>

            <v-card-actions>
            </v-card-actions>
        </v-card>

    </div>

</template>

<script>
import XLSX from 'xlsx/xlsx.js';
export default {

    data() {
        return {
            file:null,
            selectedSheet: null,
            status_file:false,
            valid: true,
            rules:{
                    file: [ v => !!v || 'Este campo es requerido!' ],
            },
            ticket: {},
            tickets: [],
            ticketsFile: [],
            loading: false,
            titleForm: null,
            search: "",
            headers: [
                { text: "Ticket",value: "code", sortable: false  },

                { text: "Estado", value: "status" },
                { text: "Comentario de cierre", value: "comment" },
                // { text: "Eliminar", sortable: false }
            ],
            request: {},
            customers:[],
            customer:{},
            types:[],
            type:{}
        };
    },
    created() {
        this.axios
                .get('/api/customers/')
                .then(response => {
                    this.customers = response.data;
                });
                this.axios
                .get('/api/types/')
                .then(response => {
                    this.types = response.data;
                });

    },
    methods: {
        helpFile(){
            this.$swal.fire({
                        title: 'Atención',
                        html: `<h4>Para realizar el cierre de tickets deberá seguir los siguientes pasos:</h4><br>
                                <ol>
                                <li>Seleccionar un cliente</li>
                                <li>Seleccionar un tipo de ticket</li>
                                <li>Seleccionar un archivo .xls o .xlsx</li>
                                </ol>
                                El archivo debe contener una sola hoja y con la siguiente estructura: <br>
                        <center>
                        <table border="1" width="100%">
                        <thead>
                        <tr>
                            <th>ticket</th>
                            <th>comentario</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>INC ####-#####1</td>
                            <td>'Comentario1'</td>
                        </tr>
                        <tr>
                            <td>INC ####-#####2</td>
                            <td>'Comentario2'</td>
                        </tr>
                        </tbody>
                        </table>
                        </center>`,
                        icon: "info",
                    showConfirmButton: true,
                    showCancelButton: true
                        });
        },
        onChange(event) {
      this.file = event.target.files ? event.target.files[0] : null;
    },
         onChangeFile() {
             var aux_tickets = [];
            var reader = new FileReader();

                reader.onload = (e) => {
                    this.loading = true;
                    var data = new Uint8Array(e.target.result);
                    var workbook = XLSX.read(data, {type: 'array'});
                    let sheetName = workbook.SheetNames[0]
                    let worksheet = workbook.Sheets[sheetName];
                    var aux = XLSX.utils.sheet_to_json(worksheet);


                     this.ticketsFile = aux.map((item) => {
                        return {
                            Id: null,
                            code: item.ticket,
                            comment: item.comentario,
                            customer_id: this.customer.Id,
                            customerName: this.customer.Name,
                            type: this.type.Id,
                            typeName: this.type.Name,
                            status: null,
                        }
                    });

                    //this.verificarTickets(aux_tickets);


                this.loading = false;
                };
                    reader.readAsArrayBuffer(this.file);
                this.status_file=true;


       },

        verificarTickets(){
           var auxTickets=[];
            this.$swal
                .fire({
                    title: "Esta seguro?",
                    html: `Estimado ${this.$store.state.user.name} a continuación enviará <strong>${this.ticketsFile.length}</strong>
                    tickets de tipo <strong>${this.type.Name}</strong> para su cierre del cliente <strong>${this.customer.Name}</strong> <br>`,
                    icon: "question",
                    showConfirmButton: true,
                    showCancelButton: true
                })
                .then(res => {
                    if (res.value) {
                      this.ticketsFile.forEach(element => {
                          this.loading = true;
                        //console.log(element);
                        this.loading=true;
                        this.$swal.fire({
                        title: 'Espere',
                        html: `Enviando tickets para el cierre...`,
                        icon: 'warning',
                        allowOutsideClick: false
                    });
                    this.$swal.showLoading();
                        this.axios
                            .post('/api/close-tickets', element)
                            .then(resp => {
                                console.log(resp.data);
                                auxTickets.push(resp.data);

                            })
                            .catch((err) => {
                                console.log(err);

                            })
                    });
                    this.$swal.fire({
                                        title: 'Correcto',
                                        html: `Tickets cerrados correctamente!`,
                                        icon: 'success',
                                        timer: 1500,
                                        timerProgressBar: true,
                                        });
                    }
                });
                    this.loading = false;
                    this.tickets =  auxTickets;


       },
        async sendMultiple(){
            //console.log(this.ticketsFile);
        var auxTickets=[];
        var enviados=0;
        this.$swal
                .fire({
                    title: "Esta seguro?",
                    html: `Estimado ${this.$store.state.user.name} a continuación enviará <strong>${this.ticketsFile.length}</strong>
                    tickets de tipo <strong>${this.type.Name}</strong> para su cierre del cliente <strong>${this.customer.Name}</strong> <br>`,
                    icon: "question",
                    showConfirmButton: true,
                    showCancelButton: true
                })
                .then(res => {
                    if (res.value) {
                        this.loading=true;
                        this.$swal.fire({
                        title: 'Espere',
                        html: `Enviando tickets para el cierre...\n
                                ${enviados} de ${this.ticketsFile.length}`,
                        icon: 'warning',
                        allowOutsideClick: false
                    });
                    this.$swal.showLoading();
                        this.ticketsFile.forEach(element => {
                            console.log(element);
                            this.axios
                                   .post('/api/close-tickets', element)
                                   .then((resp) => {
                                       console.log(resp.data);
                                       auxTickets.push(resp.data);
                                       enviados++;
                                       //this.tickets=resp.data;
                                       //this.$toasted.success("Información importada correctamente!");
                                      /*  this.$swal.fire({
                                           title: 'Correcto',
                                           html: `Tickets cerrados correctamente!`,
                                           icon: 'success',
                                           timer: 1500,
                                           timerProgressBar: true,
                                           }); */

                                   })
                                   .catch((err) => {
                                       console.log(err);
                                    })
                                   .finally(() => {

                                   })
                        });

                    }
                });
                this.tickets =  auxTickets;
                this.loading = false;
                this.ticketsFile=[];
                this.request={};
                this.file=null;

    },
     sendMultiple2(){
            console.log(this.ticketsFile);

        this.request.tickets=this.ticketsFile;
        this.request.customer_id=this.customer.Id;
        this.request.type_id=this.type.Id;
        this.request.customer_name=this.customer.Name;
        this.request.usuario=this.$store.state.user.name;
        console.log(this.request);
        this.$swal
                .fire({
                    title: "Esta seguro?",
                    html: `Estimado ${this.$store.state.user.name} a continuación enviará <strong>${this.ticketsFile.length}</strong>
                    tickets de tipo <strong>${this.type.Name}</strong> para su cierre del cliente <strong>${this.customer.Name}</strong> <br>`,
                    icon: "question",
                    showConfirmButton: true,
                    showCancelButton: true
                })
                .then(res => {
                    if (res.value) {
                        this.loading=true;
                        this.$swal.fire({
                        title: 'Espere',
                        text: 'Enviando tickets para el cierre...',
                        icon: 'warning',
                        allowOutsideClick: false
                    });
                    this.$swal.showLoading();
                         this.axios
                                .post('/api/close-tickets', this.request)
                                .then(resp => {
                                    console.log(resp.data);
                                    //this.tickets=resp.data;
                                    //this.$toasted.success("Información importada correctamente!");
                                    this.$swal.fire({
                                        title: 'Correcto',
                                        html: `Tickets cerrados correctamente!`,
                                        icon: 'success',
                                        timer: 1500,
                                        timerProgressBar: true,
                                        });

                                })
                                .catch((err) => {
                                    this.$swal.fire({
                                        title: 'Incorrecto',
                                        text: `Ocurrió un error!\n${err}`,
                                        icon: 'error',
                                        });

                                })
                                .finally(() => {
                                    this.loading = false;
                                    this.tickets=[];
                                    this.request={};
                                    this.file=null;
                                })
                            }
                });

    },
        deleteItem(el) {
            let i = this.tickets.map(data => data.id).indexOf(el.id);
            this.tickets.splice(i, 1)
        },
    }
};
</script>
