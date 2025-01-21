<template>
  <v-app-bar app flat>
    <div class="left-section">
      <img src="/logo.png" alt="Logo" class="logo" />
      <v-btn text :to="'/'" class="title-btn">Hivemind</v-btn>
    </div>

    <!-- Centered and Responsive Search Input Field -->
    <v-text-field
      class="search-field"
      placeholder="Search"
      hide-details
      solo
      flat
      prepend-inner-icon="mdi-magnify"
    ></v-text-field>

    <div class="right-section">
      <!-- Menu Items with Tooltips -->
      <v-tooltip bottom>
        <template v-slot:activator="{ on, attrs }">
          <v-btn icon :to="'/contact'" class="nav-btn" v-bind="attrs" v-on="on">
            <v-icon>mdi-phone</v-icon>
          </v-btn>
        </template>
        <span>Contact</span>
      </v-tooltip>

      <v-tooltip bottom>
        <template v-slot:activator="{ on, attrs }">
          <v-btn icon :to="'/talk'" class="nav-btn" v-bind="attrs" v-on="on">
            <v-icon>mdi-chat</v-icon>
          </v-btn>
        </template>
        <span>Talk</span>
      </v-tooltip>

      <v-tooltip bottom>
        <template v-slot:activator="{ on, attrs }">
          <v-btn icon :to="'/live'" class="nav-btn" v-bind="attrs" v-on="on">
            <v-icon>mdi-video</v-icon>
          </v-btn>
        </template>
        <span>Live Now</span>
      </v-tooltip>

      <v-tooltip bottom>
        <template v-slot:activator="{ on, attrs }">
          <v-btn icon :to="'/videos'" class="nav-btn" v-bind="attrs" v-on="on">
            <v-icon>mdi-video-outline</v-icon>
          </v-btn>
        </template>
        <span>Videos</span>
      </v-tooltip>

      <v-tooltip bottom>
        <template v-slot:activator="{ on, attrs }">
          <v-btn icon :to="'/profile'" class="nav-btn" v-bind="attrs" v-on="on">
            <v-icon>mdi-account</v-icon>
          </v-btn>
        </template>
        <span>My Profile</span>
      </v-tooltip>
    </div>
  </v-app-bar>
</template>

<script setup>
</script>

<style scoped>
.v-app-bar {
  background-color: transparent;
  box-shadow: none;
  display: flex;
  justify-content: space-between;
  padding: 0 50px;
  position: relative;
}

.left-section, .right-section {
  display: flex;
  align-items: center;
}

.left-section {
  position: absolute;
  left: 50px;
}

.right-section {
  position: absolute;
  right: 50px;
}

.v-toolbar-title {
  color: #fff;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.v-btn {
  color: #fff;
  display: flex;
  align-items: center;
  background-color: transparent;
  border: none;
  justify-content: center;
}

.nav-btn {
  margin-right: 16px;
}

.title-btn {
  margin-left: 0;
  padding-left: 0;
  max-width: 200px;
  display: flex;
  align-items: center;
}

.search-field {
  max-width: 500px; /* Make search bar wider */
  margin: 0 auto;   /* Center the search bar */
  border-radius: 25px; /* Add rounded edges */
  overflow: hidden; /* Ensure the rounded corners are visible */
  height: 50px;
}

.logo {
  width: 40px; /* Adjust the size as needed */
  height: 40px; /* Adjust the size as needed */
  margin-right: 10px; /* Space between logo and title */
}

@media (max-width: 1200px) {
  .search-field {
    max-width: 400px;
  }
}

@media (max-width: 992px) {
  .search-field {
    max-width: 300px;
  }
}

@media (max-width: 768px) {
  .search-field {
    max-width: 200px;
  }
}

@media (max-width: 576px) {
  .search-field {
    max-width: 150px;
  }

  .nav-btn {
    margin-right: 8px; /* Reduce margin between icons */
  }
}
</style>
