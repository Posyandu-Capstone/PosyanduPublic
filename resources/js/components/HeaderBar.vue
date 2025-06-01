<template>
    <div class="top-header">
      <h2 class="page-title">{{ title }}</h2>
    </div>
  </template>
  
  <script>
  export default {
    name: 'HeaderBar',
    props: {
      title: {
        type: String,
        required: true
      }
    }
  }
  </script>
  
  <style scoped>

.top-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.page-title {
  font-size: 20px;
  font-weight: 600;
  color: #111827;
  margin-bottom: -3px;
}

</style>