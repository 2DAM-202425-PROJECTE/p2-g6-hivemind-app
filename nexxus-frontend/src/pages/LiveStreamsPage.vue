<template>
  <div class="live-streams-container">
    <Navbar />
    <div class="live-streams-content">
      <!-- Sidebar (like Twitch's left navigation) -->
      <div class="sidebar">
        <h2>Categories</h2>
        <ul class="category-list">
          <li class="category-item">Test Gaming</li>
          <li class="category-item">Test Music</li>
          <li class="category-item">Test Art</li>
          <li class="category-item">Test Chat</li>
          <li class="category-item disabled">More Coming Soon...</li>
        </ul>
        <h2>Followed Channels</h2>
        <ul class="streamer-list">
          <li class="streamer-item">
            <img src="https://via.placeholder.com/30" class="streamer-pic" alt="Streamer" />
            <span>Test Streamer 1</span>
            <span class="live-dot"></span>
          </li>
          <li class="streamer-item">
            <img src="https://via.placeholder.com/30" class="streamer-pic" alt="Streamer" />
            <span>Test Streamer 2</span>
          </li>
          <li class="streamer-item disabled">
            <img src="https://via.placeholder.com/30" class="streamer-pic" alt="Streamer" />
            <span>Test Streamer 3 (Offline)</span>
          </li>
        </ul>
        <div class="sidebar-footer">
          <button class="test-btn" disabled>Test Feature</button>
          <p class="test-stats">Test Streams: 0</p>
        </div>
      </div>

      <!-- Main Content Area -->
      <div class="main-content">
        <div class="header-section">
          <div class="nav-tabs">
            <button class="tab-btn active" disabled>Following (Test)</button>
            <button class="tab-btn" disabled>Browse (Test)</button>
          </div>
          <div class="search-bar">
            <input type="text" placeholder="Search streams (Test)" disabled />
            <i class="mdi mdi-magnify search-icon"></i>
          </div>
          <div class="go-live-section">
            <button class="go-live-btn" disabled>Go Live (Coming Soon)</button>
          </div>
        </div>

        <!-- Mock Stream Tags Filter -->
        <div class="stream-tags">
          <h3>Filter by Tags (Test)</h3>
          <div class="tags-list">
            <span class="tag">Test Tag 1</span>
            <span class="tag">Test Tag 2</span>
            <span class="tag">Test Tag 3</span>
            <span class="tag disabled">More Tags Soon</span>
            <button class="tag-btn" disabled>Test Filter</button>
          </div>
        </div>

        <!-- Mock Top Streams Carousel -->
        <div class="top-streams">
          <h3>Top Streams (Test)</h3>
          <div class="carousel">
            <div class="carousel-item">
              <div class="carousel-thumbnail">Test Stream 1</div>
              <p class="carousel-title">Test Streamer 1</p>
              <p class="carousel-viewers">0 viewers</p>
            </div>
            <div class="carousel-item">
              <div class="carousel-thumbnail">Test Stream 2</div>
              <p class="carousel-title">Test Streamer 2</p>
              <p class="carousel-viewers">0 viewers</p>
            </div>
            <div class="carousel-item">
              <div class="carousel-thumbnail">Test Stream 3</div>
              <p class="carousel-title">Test Streamer 3</p>
              <p class="carousel-viewers">0 viewers</p>
            </div>
            <div class="carousel-item disabled">
              <div class="carousel-thumbnail">Coming Soon</div>
              <p class="carousel-title">More Streams</p>
              <p class="carousel-viewers">N/A</p>
            </div>
          </div>
        </div>

        <!-- Coming Soon Main Placeholder -->
        <div class="coming-soon">
          <h2>Coming Soon!</h2>
          <p>Live streaming features are on the way. Stay tuned for updates!</p>
          <div class="test-actions">
            <button class="test-action-btn" disabled>Test Live Now</button>
            <button class="test-action-btn" disabled>Test Schedule</button>
            <button class="test-action-btn" disabled>Test Clips</button>
          </div>
        </div>

        <!-- Mock Recommended Channels -->
        <div class="recommended-channels">
          <h3>Live Channels We Think You’ll Like (Test)</h3>
          <div class="mock-grid">
            <div class="mock-card">
              <div class="mock-thumbnail">Test Channel A</div>
              <p>Test Streamer A</p>
              <span class="mock-viewers">0 viewers</span>
            </div>
            <div class="mock-card">
              <div class="mock-thumbnail">Test Channel B</div>
              <p>Test Streamer B</p>
              <span class="mock-viewers">0 viewers</span>
            </div>
            <div class="mock-card">
              <div class="mock-thumbnail">Test Channel C</div>
              <p>Test Streamer C</p>
              <span class="mock-viewers">0 viewers</span>
            </div>
            <div class="mock-card disabled">
              <div class="mock-thumbnail">More Soon</div>
              <p>Coming Soon</p>
              <span class="mock-viewers">N/A</span>
            </div>
          </div>
        </div>

        <!-- Mock Featured Events -->
        <div class="featured-events">
          <h3>Featured Events (Test)</h3>
          <div class="events-grid">
            <div class="event-card">
              <div class="event-thumbnail">Test Event 1</div>
              <p class="event-title">Test Tournament</p>
              <p class="event-date">TBD</p>
            </div>
            <div class="event-card">
              <div class="event-thumbnail">Test Event 2</div>
              <p class="event-title">Test Concert</p>
              <p class="event-date">TBD</p>
            </div>
            <div class="event-card disabled">
              <div class="event-thumbnail">More Soon</div>
              <p class="event-title">Coming Soon</p>
              <p class="event-date">N/A</p>
            </div>
          </div>
        </div>

        <!-- Mock Stream Categories -->
        <div class="stream-categories">
          <h3>Test Stream Categories</h3>
          <div class="categories-grid">
            <div class="category-card">
              <div class="category-thumbnail">Test Gaming</div>
              <p>Test Streams: 0</p>
            </div>
            <div class="category-card">
              <div class="category-thumbnail">Test Music</div>
              <p>Test Streams: 0</p>
            </div>
            <div class="category-card">
              <div class="category-thumbnail">Test Art</div>
              <p>Test Streams: 0</p>
            </div>
            <div class="category-card disabled">
              <div class="category-thumbnail">More Soon</div>
              <p>N/A</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <Footer />
  </div>
</template>

<script setup>
import Navbar from '@/components/NavBar.vue';
import Footer from '@/components/AppFooter.vue';
</script>

<style scoped>
.live-streams-container {
  font-family: Arial, sans-serif;
  padding: 20px;
  padding-top: 90px; /* Space for fixed navbar */
  padding-bottom: 80px; /* Space for footer */
  background-color: #f0f2f5;
  min-height: 100vh;
  color: black;
}

.live-streams-content {
  display: flex;
  max-width: 1200px; /* Wider layout like Twitch */
  margin: 0 auto;
  gap: 20px;
}

/* Sidebar Styles */
.sidebar {
  width: 250px;
  flex-shrink: 0;
  background: #ffffff;
  border-radius: 24px;
  padding: 15px;
  border: 1px solid #ddd;
}

.sidebar h2 {
  font-size: 18px;
  margin-bottom: 10px;
  color: #333;
}

.category-list,
.streamer-list {
  list-style: none;
  padding: 0;
  margin: 0 0 20px 0;
}

.category-item,
.streamer-item {
  padding: 10px;
  cursor: pointer;
  border-radius: 5px;
  transition: background-color 0.2s;
  display: flex;
  align-items: center;
  gap: 10px;
}

.category-item:hover,
.streamer-item:hover {
  background-color: #e0e0e0;
}

.streamer-pic {
  width: 30px;
  height: 30px;
  border-radius: 50%;
}

.live-dot {
  width: 8px;
  height: 8px;
  background-color: #ff0000; /* Twitch-like live indicator */
  border-radius: 50%;
}

.disabled {
  color: #999;
  cursor: not-allowed;
  opacity: 0.6;
}

.sidebar-footer {
  margin-top: 20px;
}

.test-btn {
  width: 100%;
  padding: 10px;
  background-color: #ccc;
  color: #666;
  border: none;
  border-radius: 5px;
  cursor: not-allowed;
}

.test-stats {
  font-size: 12px;
  color: #666;
  text-align: center;
  margin-top: 10px;
}

/* Main Content Styles */
.main-content {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.header-section {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 20px;
}

.nav-tabs {
  display: flex;
  gap: 10px;
}

.tab-btn {
  padding: 10px 20px;
  background-color: #ccc;
  color: #666;
  border: none;
  border-radius: 5px;
  cursor: not-allowed;
  font-size: 16px;
}

.tab-btn.active {
  background-color: #e0e0e0;
  color: #333;
}

.search-bar {
  position: relative;
  flex-grow: 1;
  max-width: 300px;
}

.search-bar input {
  width: 100%;
  padding: 10px 10px 10px 40px;
  border: 1px solid #ddd;
  border-radius: 5px;
  background-color: #f9f9f9;
  color: #999;
  cursor: not-allowed;
}

.search-icon {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 20px;
  color: #999;
}

.go-live-section {
  flex-shrink: 0;
}

.go-live-btn {
  padding: 10px 20px;
  background-color: #ccc; /* Greyed out for disabled state */
  color: #666;
  border: none;
  border-radius: 5px;
  cursor: not-allowed;
  font-size: 16px;
}

.go-live-btn:hover {
  background-color: #ccc; /* No hover effect since disabled */
}

/* Stream Tags Filter */
.stream-tags {
  background: #ffffff;
  border-radius: 24px;
  padding: 15px;
  border: 1px solid #ddd;
}

.stream-tags h3 {
  font-size: 18px;
  margin-bottom: 15px;
  color: #333;
}

.tags-list {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.tag {
  padding: 5px 10px;
  background-color: #e0e0e0;
  border-radius: 15px;
  font-size: 14px;
  color: #333;
}

.tag.disabled {
  background-color: #ccc;
}

.tag-btn {
  padding: 5px 15px;
  background-color: #ccc;
  color: #666;
  border: none;
  border-radius: 5px;
  cursor: not-allowed;
}

/* Top Streams Carousel */
.top-streams {
  background: #ffffff;
  border-radius: 24px;
  padding: 15px;
  border: 1px solid #ddd;
}

.top-streams h3 {
  font-size: 18px;
  margin-bottom: 15px;
  color: #333;
}

.carousel {
  display: flex;
  gap: 15px;
  overflow-x: auto;
  padding-bottom: 10px;
}

.carousel-item {
  flex-shrink: 0;
  width: 200px;
  text-align: center;
}

.carousel-thumbnail {
  height: 120px;
  background-color: #ddd;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #666;
  font-size: 14px;
}

.carousel-title {
  margin-top: 5px;
  font-size: 14px;
  color: #333;
}

.carousel-viewers {
  margin-top: 2px;
  font-size: 12px;
  color: #666;
}

/* Coming Soon Section */
.coming-soon {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  background: #ffffff;
  border-radius: 24px;
  border: 1px solid #ddd;
  padding: 60px;
  text-align: center;
}

.coming-soon h2 {
  font-size: 48px; /* Big and bold */
  color: #333;
  margin-bottom: 20px;
}

.coming-soon p {
  font-size: 20px;
  color: #666;
  margin-bottom: 20px;
}

.test-actions {
  display: flex;
  gap: 15px;
}

.test-action-btn {
  padding: 10px 20px;
  background-color: #ccc;
  color: #666;
  border: none;
  border-radius: 5px;
  cursor: not-allowed;
  font-size: 16px;
}

/* Recommended Channels Section */
.recommended-channels {
  background: #ffffff;
  border-radius: 24px;
  padding: 15px;
  border: 1px solid #ddd;
}

.recommended-channels h3 {
  font-size: 18px;
  margin-bottom: 15px;
  color: #333;
}

.mock-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 15px;
}

.mock-card {
  text-align: center;
}

.mock-thumbnail {
  height: 100px;
  background-color: #ddd;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #666;
  font-size: 14px;
}

.mock-card p {
  margin-top: 5px;
  font-size: 14px;
  color: #333;
}

.mock-viewers {
  font-size: 12px;
  color: #666;
}

/* Featured Events Section */
.featured-events {
  background: #ffffff;
  border-radius: 24px;
  padding: 15px;
  border: 1px solid #ddd;
}

.featured-events h3 {
  font-size: 18px;
  margin-bottom: 15px;
  color: #333;
}

.events-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 15px;
}

.event-card {
  text-align: center;
}

.event-thumbnail {
  height: 120px;
  background-color: #ddd;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #666;
  font-size: 14px;
}

.event-title {
  margin-top: 5px;
  font-size: 14px;
  color: #333;
}

.event-date {
  font-size: 12px;
  color: #666;
}

/* Stream Categories Section */
.stream-categories {
  background: #ffffff;
  border-radius: 24px;
  padding: 15px;
  border: 1px solid #ddd;
}

.stream-categories h3 {
  font-size: 18px;
  margin-bottom: 15px;
  color: #333;
}

.categories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 15px;
}

.category-card {
  text-align: center;
}

.category-thumbnail {
  height: 100px;
  background-color: #ddd;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #666;
  font-size: 14px;
}

.category-card p {
  margin-top: 5px;
  font-size: 14px;
  color: #333;
}
</style>
