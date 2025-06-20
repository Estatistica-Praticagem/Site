<template>
  <section class="section-login flex flex-center">
    <div class="login-content">
      <h2 class="text-h5 text-primary text-center q-mb-md">Login</h2>

      <q-form @submit.prevent="login" class="login-form">
        <q-input
          v-model="email"
          label="Email"
          type="email"
          filled
          required
          class="q-mb-md"
        />
        <q-input
          v-model="password"
          label="Password"
          type="password"
          filled
          required
          class="q-mb-md"
        />

        <q-btn
          label="Login"
          type="submit"
          color="primary"
          class="full-width"
          :loading="loading"
        />
      </q-form>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { Notify } from 'quasar';

const email = ref('');
const password = ref('');
const loading = ref(false);
const router = useRouter();

async function login() {
  loading.value = true;

  try {
    const response = await fetch('https://www.meusimulador.com/kevi/backend/login.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: email.value, password: password.value }),
    });

    const data = await response.json();

    if (data.success) {
      localStorage.setItem('user_id', data.id);
      localStorage.setItem('user_name', data.name);
      Notify.create({ type: 'positive', message: 'Login successful!' });
      router.push('/contacts');
    } else {
      Notify.create({ type: 'negative', message: data.message || 'Login failed.' });
    }
  } catch (err) {
    Notify.create({ type: 'negative', message: 'Connection error.' });
    console.error(err);
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.section-login {
  min-height: 100vh;
  background: transparent;
  padding: 60px 20px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.login-content {
  max-width: 400px;
  width: 100%;
  background: #ffffffee;
  backdrop-filter: blur(6px);
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.full-width {
  width: 100%;
}
</style>
