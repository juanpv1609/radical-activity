<template>
  <div>
    
    <section>
      <h3>Import XLSX</h3>
      <input type="file" @change="onChange" />
      <xlsx-read :file="file">
        <xlsx-sheets>
          <template #default="{sheets}">
            <select v-model="selectedSheet">
              <option v-for="sheet in sheets" :key="sheet" :value="sheet">
                {{ sheet }}
              </option>
            </select>
          </template>
        </xlsx-sheets>
        <xlsx-table :sheet="selectedSheet" />
        <xlsx-json :sheet="selectedSheet">
          <template #default="{collection}">
            <div>
              {{ collection }}
            </div>
          </template>
        </xlsx-json>
      </xlsx-read>
    </section>
  </div>
</template>

<script>
import { XlsxRead, XlsxTable, XlsxSheets, XlsxJson, XlsxWorkbook, XlsxSheet, XlsxDownload } from "vue-xlsx/dist/vue-xlsx.es.js"

export default {
  components: {
    XlsxRead,
    XlsxTable,
    XlsxSheets,
    XlsxJson,
    XlsxWorkbook,
    XlsxSheet,
    XlsxDownload
  },
  data() {
    return {
      file: null,
      selectedSheet: null,
      sheetName: null,
      sheets: [{ name: "SheetOne", data: [{ c: 2 }] }],
      collection: [{ a: 1, b: 2 }]
    };
  },
  methods: {
    onChange(event) {
      this.file = event.target.files ? event.target.files[0] : null;
    },
    addSheet() {
      this.sheets.push({ name: this.sheetName, data: [...this.collection] });
      this.sheetName = null;
    }
  }
};
</script>
