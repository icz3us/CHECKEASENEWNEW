<template>
  <div class=" postion-fixed">
    <!-- Hamburger Icon for Mobile -->
    <button
      class="btn d-md-none"
      @click="toggleSidebar"
      style="position: fixed; top: 10px; left: 10px; z-index: 1001; background: none; border: none; padding: 0;"
    >
      <i class="material-icons" style="color: black;">menu</i>
    </button>

    <!-- Sidebar -->
    <div
      :class="['sidebar', { 'sidebar-hidden': isSidebarHidden }]"
      style="width: 250px; height: 100vh;"
    >
      <!-- Logo Section -->
      <div class="text-center py-3">
        <img src="/public/images/checkEaseLogo.png" alt="Check Ease Logo" width="150" />
      </div>

      <!-- Menu Items -->
      <ul class="list-unstyled mt-4">
        <li>
          <router-link
            to="/home"
            class="sidebar-item"
            :class="{ active: isActive('/home') }"
          >
            <i class="material-icons">dashboard</i> Dashboard
          </router-link>
        </li>
        <li>
          <router-link
            to="/class"
            class="sidebar-item"
            :class="{ active: isActive('/class') }"
          >
            <i class="material-icons">add_circle</i> Create Class
          </router-link>
        </li>

        <li>
          <router-link
            to="/attendance"
            class="sidebar-item"
            :class="{ active: isActive('/attendance') }"
          >
            <i class="material-icons">event</i> Attendance
          </router-link>
        </li>

        <li>
          <router-link
            to="/clearance"
            class="sidebar-item"
            :class="{ active: isActive('/clearance') }"
          >
            <i class="material-icons">assignment</i> Clearance
          </router-link>
        </li>
      </ul>

      <!-- Logout Icon Fixed at Bottom -->
      <div class="logout-icon" @click="logout">
        <i class="material-icons">exit_to_app</i> <b>Logout</b>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'; 

export default {
  data() {
    return {
      isSidebarHidden: false,
      isAttendanceDropdownOpen: false,
      currentRoute: '/home',
      showDialog: false,
    };
  },

  mounted() {
    this.handleResize();
    this.loadUserInfo();
    console.log(localStorage.getItem('email'));
    window.addEventListener('resize', this.handleResize);
  },

  beforeDestroy() {
    window.removeEventListener('resize', this.handleResize);
  },

  methods: {
    toggleSidebar() {
      this.isSidebarHidden = !this.isSidebarHidden;  
    },

    confirmLogout() {
      this.showDialog = true;
    },

    closeDialog() {
      this.showDialog = false;
    },

    logout() {
      
      axios.get('/logout.php') 
        .then(response => {
          localStorage.removeItem("token");
          localStorage.removeItem("role");
          localStorage.removeItem("firstname");
          localStorage.removeItem("lastname");
          localStorage.removeItem("email");

          this.$router.push("/Login");
          this.showDialog = false; 
        })
        .catch(error => {
          console.error('Logout failed:', error);
        });
    },

    toggleAttendanceDropdown() {
      this.isAttendanceDropdownOpen = !this.isAttendanceDropdownOpen;
    },

    handleResize() {
      this.isSidebarHidden = window.innerWidth < 768;
    },

    isActive(route) {
      return this.$route.path === route;
    },

    loadUserInfo() {
      const firstName = localStorage.getItem('firstname') || 'Guest';
      const lastName = localStorage.getItem('lastname') || '';
      const email = localStorage.getItem('email') || '';
      this.userInfo = { firstName, lastName, email };
    },
  },
};
</script>

<style scoped>
.sidebar {
  background-color: #DBF4F8;
  
  color: #000;
  position: fixed;
  top: 0;
  left: 0;
  overflow-y: auto;
  transition: transform 0.3s ease;
  z-index: 1000;
  display: flex;
  flex-direction: column;
  padding-top: 20px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5) !important;
}

.sidebar-hidden {
  transform: translateX(-100%);
}

.sidebar-item {
  padding: 18px;
  font-size: 20px;
  cursor: pointer;
  color: gray;
  display: flex;
  align-items: center;
  text-decoration: none;
}

.sidebar-item i {
  font-size: 26px;
  margin-right: 12px;
}

.sidebar-item.active {
  color: black;
}

.sidebar-item:hover {
  color: black;
}

.sidebar-item:not(.active) {
  color: gray;
}

.logout-icon {
  font-size: 24px;
  cursor: pointer;
  text-align: center;
  padding: 18px;
  color: #000;
  position: absolute;
  bottom: 0;
  width: 100%;
}

button {
  background: none;
  border: none;
  padding: 0;
}

@media (max-width: 768px) {
  .sidebar {
    width: 100%;
    height: auto;
    position: fixed;
    top: 0;
    left: 0;
    box-shadow: none;
    z-index: 1000; 
  }

  .sidebar-hidden {
    transform: translateY(-100%);
  }

  .sidebar-item {
    padding: 10px;
  }

  .logout-icon {
    position: relative;
    bottom: auto;
  }
}
</style>
