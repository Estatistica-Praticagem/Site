<template>
  <footer class="footer-company q-py-md">
    <div class="row items-center justify-center full-width gap-3">
      <q-img
        src="~assets/icons/icon.png"
        alt="Logo Horizonte BI"
        class="footer-logo"
        spinner-color="primary"
        fit="contain"
      />
      <div>
        <div class="text-h6 text-primary" style="letter-spacing:1px;">Horizonte BI</div>
        <div class="text-caption text-grey-7" style="margin-top:-4px;">Global Solutions, Local Results</div>
      </div>
    </div>
  </footer>
</template>

<script setup>
// Nenhum script é necessário para este rodapé
</script>

<style scoped>
.footer-company {
  background: #f6f8f8;
  border-top: 1px solid #e3eaf0;
  box-shadow: 0 -1px 8px #90caf915;
  min-height: 62px;
  width: 100%;
}
.footer-logo {
  width: 52px;
  height: 46px;
  margin-right: 18px;
  border-radius: 9px;
  box-shadow: 0 2px 8px #1976d21c;
  background: #fff;
}
@media (max-width: 600px) {
  .footer-logo {
    width: 38px;
    height: 32px;
    margin-right: 10px;
  }
  .text-h6 { font-size: 1.05em; }
}
</style>
