import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router, ActivatedRoute, RouterLink } from '@angular/router';
import { CommonModule } from '@angular/common';
import { AuthService } from '../services/auth.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css'],
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    RouterLink
  ]
})
export class LoginComponent implements OnInit {
  loginForm: FormGroup;
  submitted = false;
  loading = false;
  loginError = '';
  registrationSuccess = false;
  registeredEmail: string = '';

  constructor(
    private formBuilder: FormBuilder,
    private router: Router,
    private route: ActivatedRoute,
    private authService: AuthService
  ) {
    this.loginForm = this.formBuilder.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', Validators.required],
      rememberMe: [false]
    });
  }

  get f() { return this.loginForm.controls; }

  ngOnInit() {
    this.route.queryParams.subscribe(params => {
      this.registrationSuccess = params['registered'] === 'true';
      this.registeredEmail = params['email'] || '';
      if (this.registeredEmail) {
        this.loginForm.patchValue({ email: this.registeredEmail });
      }
    });
  }

  onSubmit() {
    this.submitted = true;
    this.loginError = '';

    if (this.loginForm.invalid) {
      Object.keys(this.loginForm.controls).forEach(key => {
        this.loginForm.get(key)?.markAsTouched();
      });
      return;
    }

    this.loading = true;
    try {
      const success = this.authService.login(
        this.f['email'].value,
        this.f['password'].value
      );

      setTimeout(() => {
        if (success) {
          this.router.navigate(['/dashboard']);
        } else {
          this.loginError = 'Invalid email or password';
        }
        this.loading = false;
      }, 1000);
    } catch (error) {
      this.loading = false;
      this.loginError = 'An error occurred during login';
      console.error('Login error:', error);
    }
  }
}
