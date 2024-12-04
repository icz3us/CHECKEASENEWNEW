<template>
  <div class="header-container">
    <div class="card header-card">
      <div class="card-body d-flex align-items-center justify-content-between">
        <h3 class="card-title mb-0 d-none d-sm-block"><b>{{ cardTitle }}</b></h3>

        <div class="profile-section d-flex align-items-center">
          <!-- Profile Picture -->
          <div class="profile-picture-container d-flex justify-content-center align-items-center position-relative">
            <img 
              v-if="userInfo.profilePicture"
              :src="userInfo.profilePicture"
              alt="Profile Picture"
              class="profile-picture"
              @click="uploadProfilePicture"
            />
            <input 
              type="file" 
              ref="profilePictureInput" 
              accept="image/*" 
              class="d-none" 
              @change="handleProfilePictureChange" 
            />
          </div>

          <!-- User Info Section -->
          <div class="user-info d-flex align-items-center">
            <i class="material-icons">account_circle</i>
            <div class="account-info d-flex flex-column justify-content-center bg-light px-3 shadow-account"
               style="height: 50px; width: 270px; border-radius: 20px;">
              <span class="fw-bold">{{ userInfo.lastName }}, {{ userInfo.firstName }}</span>
              <span>{{ userInfo.email }}</span>
            </div>
          </div>

          <!-- Edit Profile Dropdown -->
          <span class="material-icons ms-3" @click="openEditProfileDialog" style="cursor: pointer;">
            edit
          </span>
        </div>
      </div>
    </div>

    <!-- Edit Profile Modal -->
    <div v-if="showEditProfile" class="modal-overlay" @click.self="closeEditProfileDialog">
      <div class="modal-dialog">
        <div class="modal-header">
          <h5 class="modal-title">Edit Profile</h5>
          <button type="button" class="btn-close" @click="closeEditProfileDialog"></button>
        </div>
        <div class="modal-body">
          <!-- Profile Picture Upload Section -->
          <div class="profile-upload-container text-center mb-4">
            <input 
              type="file" 
              ref="modalProfilePictureInput" 
              accept="image/*" 
              class="d-none" 
              @change="handleModalProfilePictureChange" 
            />
            <img 
              :src="modalProfilePicture || (userInfo.profilePicture || 'default-profile.png')" 
              alt="Profile Picture" 
              class="modal-profile-picture" 
              @click="triggerProfilePictureUpload"
            />
            <div class="upload-overlay" @click="triggerProfilePictureUpload">
              <span class="material-icons">camera_alt</span>
            </div>
          </div>

          <div class="form-group">
            <label for="firstName">First Name</label>
            <input type="text" id="firstName" v-model="userInfo.firstName" disabled class="form-control" />
          </div>
          <div class="form-group">
            <label for="lastName">Last Name</label>
            <input type="text" id="lastName" v-model="userInfo.lastName" disabled class="form-control" />
          </div>
          <div class="form-group">
            <label for="middleName">Middle Name</label>
            <input type="text" id="middleName" v-model="userInfo.middleName" disabled class="form-control" />
          </div>
          <div class="form-group">
            <label for="birthday">Birthday</label>
            <input type="date" id="birthday" v-model="userInfo.birthday" class="form-control" />
          </div>
          <div class="form-group">
            <label for="gender">Gender</label>
            <select id="gender" v-model="userInfo.gender" class="form-control">
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>
          <div class="form-group">
            <label for="collegeDepartment">College Department</label>
            <select id="collegeDepartment" v-model="userInfo.collegeDepartment" class="form-control">
              <option value="College of Computer Studies">College of Computer Studies</option>
              <option value="CHTM">CHTM</option>
              <option value="BRRRT">BRRRT</option>
              <option value="EXAMPLE">EXAMPLE</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="closeEditProfileDialog">Close</button>
          <button type="button" class="btn btn-primary" @click="saveProfileChanges">Save changes</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      userInfo: {
        firstName: '',
        lastName: '',
        middleName: '',
        birthday: null,
        gender: 'Male',
        collegeDepartment: 'College of Computer Studies',
        email: '',
        profilePicture: null,
      },
      showEditProfile: false,
      modalProfilePicture: null,
    };
  },
  computed: {
    cardTitle() {
      switch (this.$route.path) {
        case '/clearance':
          return 'Clearance';
        case '/ClearanceRecord':
          return 'Clearance';
        case '/attendance':
          return 'Attendance';
        case '/home':
          return 'Dashboard';
        case '/class':
          return 'Create & View Class';
        case '/ViewStudentAttendance':
          return 'View Student Attendance';
        default:
          return 'Dashboard';
      }
    },
  },
  methods: {
    triggerProfilePictureUpload() {
      this.$refs.modalProfilePictureInput.click();
    },
    handleModalProfilePictureChange(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          this.modalProfilePicture = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    },
    openEditProfileDialog() {
      // Reset modal profile picture when opening dialog
      this.modalProfilePicture = this.userInfo.profilePicture;
      this.showEditProfile = true;
    },
    closeEditProfileDialog() {
      this.showEditProfile = false;
      this.modalProfilePicture = null;
    },
    saveProfileChanges() {
      // Update profile picture if changed
      if (this.modalProfilePicture) {
        this.userInfo.profilePicture = this.modalProfilePicture;
      }

      // Handle saving logic here
      console.log('Profile saved', this.userInfo);
      this.closeEditProfileDialog();
    },
    loadUserInfo() {
      const firstName = localStorage.getItem('firstname') || 'Guest';
      const lastName = localStorage.getItem('lastname') || '';
      const middleName = localStorage.getItem('middleName') || '';
      const email = localStorage.getItem('email') || '';
      const profilePicture = localStorage.getItem('profilePicture') || null;

      this.userInfo = { 
        firstName, 
        lastName, 
        middleName, 
        email, 
        profilePicture,
        gender: 'Male',
        collegeDepartment: 'College of Computer Studies',
        birthday: null,
      };
    },
  },
  mounted() {
    this.loadUserInfo();
    window.addEventListener('storage', this.loadUserInfo);
  },
  beforeUnmount() {
    window.removeEventListener('storage', this.loadUserInfo);
  },
};
</script>


<style scoped>
.header-container {
  position: fixed;
  top: 0;
  right: 0;
  left: 250px;
  width: calc(100% - 250px);
  padding: 10px 20px;
  z-index: 1000;
  transition: all 0.3s ease;
}

.header-card {
  background-color: #DBF4F8;
  border-radius: 15px;
  width: 100%;
  height: auto;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.card-body {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
}

.card-title {
  font-size: 1.5rem;
  color: black;
}

.profile-section {
  display: flex;
  align-items: center;
  font-size: 14px;
}

.user-info {
  font-size: 14px;
  color: black;
}

.material-icons {
  font-size: 24px;
  color: black;
}

.profile-section i.material-icons {
  font-size: 24px;
}

.user-info i.material-icons {
  font-size: 36px;
  color: black;
  margin-right: 8px;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999; 
  pointer-events: auto; 
}

.modal-dialog {
  background-color: white;
  padding: 20px;
  border-radius: 10px;
  width: 400px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  z-index: 10000; 
  pointer-events: auto; 
  position: relative; 
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-title {
  font-size: 1.25rem;
}

.btn-close {
  border: none;
  background: transparent;
  font-size: 1.5rem;
}

.modal-body .form-group {
  margin-bottom: 1rem;
}

.form-control {
  width: 100%;
  padding: 8px;
  border-radius: 5px;
  border: 1px solid #ccc;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
}


@media (max-width: 576px) {
  .header-container {
    left: 0;
    width: 100%;
    padding: 10px;
    z-index: 1000;
  }

  .modal-dialog {
    width: 90%;
  }
}
.profile-upload-container {
  position: relative;
  margin-bottom: 20px;
}

.modal-profile-picture {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  object-fit: cover;
  cursor: pointer;
  border: 3px solid #DBF4F8;
}

.upload-overlay {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: rgba(0, 0, 0, 0.5);
  width: 150px;
  height: 150px;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  opacity: 0;
  transition: opacity 0.3s ease;
  cursor: pointer;
}

.upload-overlay:hover {
  opacity: 1;
}

.upload-overlay .material-icons {
  color: white;
  font-size: 36px;
}

.modal-overlay {
  cursor: pointer;
}

.modal-dialog {
  cursor: default;
}
</style>
