<template>
  <div class="home-container">
    <h1>Home</h1>
    <div class="stories">
      <div class="story" v-for="(story, index) in stories" :key="index">
        <img :src="story.image" :alt="story.name" />
        <p>{{ story.name }}</p>
      </div>
    </div>

    <div class="post-card">
      <div class="post-header">
        <img class="profile-pic" src="https://" alt="Profile" />
        <div class="post-info">
          <h3>Radostin Ivanov</h3>
          <p>IES EBRE</p>
        </div>
      </div>

      <img class="post-image" src="https://plus.unsplash.com/premium_photo-1672115680958-54438df0ab82?fm=jpg&q=60&w=3000&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8bW91bnRhaW5zfGVufDB8fDB8fHww" alt="Post" />

      <div class="post-actions">
        <div class="likes">
          <span>3K</span> <i class="fa fa-thumbs-up"></i>
        </div>
        <div class="comments">
          <span>256</span> <i class="fa fa-comment"></i>
        </div>
        <div class="share">
          <i class="fa fa-share"></i>
        </div>
      </div>

      <div class="footer-icons">
        <i class="fa fa-plus"></i>
        <i class="fa fa-search"></i>
        <i class="fa fa-ellipsis-h"></i>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      stories: [
        { name: "My Story", image: "" },
        { name: "mendez", image: "https://via.placeholder.com/50" },
        { name: "harrywhite", image: "https://via.placeholder.com/50" },
        { name: "garadm", image: "https://via.placeholder.com/50" },
      ],
    };
  },
};
</script>

<style scoped>
.home-container {
  font-family: Arial, sans-serif;
  padding: 20px;
  background-image: url('../../public/atencionalclientequeescomomejorar.webp');
  background-size: cover;
  background-position: center;
  min-height: 100vh;
  color: white;
}

h1 {
  font-size: 24px;
}

.stories {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}

.story {
  text-align: center;
}

.story img {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  border: 2px solid #42b883;
}

.story p {
  margin-top: 5px;
  color: white;
}

.post-card {
  background: #f0f0f0;
  border: 1px solid #d3d3d3; /* Add a slightly gray border */
  border-radius: 10px;
  padding: 15px;
  width: 300px;
  margin: 0 auto;
}
.home-container {
  font-family: Arial, sans-serif;
  padding: 20px;
  background-color: white; /* Set background to white */
  min-height: 100vh;
  color: black; /* Change text color to black for better contrast */
}
.post-header {
  display: flex;
  gap: 10px;
  align-items: center;
  margin-bottom: 15px;
}

.profile-pic {
  width: 50px;
  height: 50px;
  border-radius: 50%;
}

.post-info h3 {
  font-size: 16px;
  margin: 0;
}

.post-info p {
  font-size: 12px;
  margin: 0;
}

.post-image {
  width: 100%;
  border-radius: 10px;
  margin-bottom: 15px;
}

.post-actions {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
  margin-bottom: 15px;
}

.post-actions i {
  margin-left: 5px;
}

.footer-icons {
  display: flex;
  justify-content: space-around;
}

.footer-icons i {
  font-size: 18px;
}
</style>
