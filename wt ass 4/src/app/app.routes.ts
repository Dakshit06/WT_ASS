import { Routes } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { RegistrationComponent } from './registration/registration.component';
import { ForgotPasswordComponent } from './forgot-password/forgot-password.component';

export const routes: Routes = [
  { path: '', redirectTo: '/login', pathMatch: 'full' },
  { 
    path: 'login',
    component: LoginComponent,
    title: 'Login'
  },
  { 
    path: 'register',
    component: RegistrationComponent,
    title: 'Register'
  },
  { 
    path: 'forgot-password',
    component: ForgotPasswordComponent,
    title: 'Forgot Password'
  },
  { 
    path: '**', 
    redirectTo: '/login' 
  }
];
