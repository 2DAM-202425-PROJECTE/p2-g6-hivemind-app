<template>
  <div class="modal-overlay" @click.self="closeModal">
    <div class="modal-content">
      <h2>Share Profile</h2>
      <ul>
        <li><a :href="`https://www.facebook.com/sharer/sharer.php?u=${shareUrl}`" target="_blank">Facebook</a></li>
        <li><a :href="`https://twitter.com/intent/tweet?url=${shareUrl}`" target="_blank">Twitter</a></li>
        <li><a :href="`https://www.linkedin.com/shareArticle?mini=true&url=${shareUrl}`" target="_blank">LinkedIn</a></li>
        <li><a :href="`mailto:?subject=Check out this profile&body=${shareUrl}`" target="_blank">Email</a></li>
      </ul>
      <button @click="closeModal">Close</button>
    </div>
  </div>
</template>

<script>
export default {
  props: ['shareUrl'],
  methods: {
    closeModal() {
      this.$emit('close');
    },
  },
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-content {
  background: white;
  padding: 20px;
  border-radius: 8px;
  text-align: center;
}

.modal-content ul {
  list-style: none;
  padding: 0;
}

.modal-content li {
  margin: 10px 0;
}

.modal-content a {
  text-decoration: none;
  color: #706e6e;
}

.modal-content button {
  margin-top: 20px;
  padding: 10px 20px;
  background-color: #706e6e;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
</style>
