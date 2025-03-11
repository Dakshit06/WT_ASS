import { AuthService } from '../services/auth.service';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { FormGroup, FormBuilder, Validators, ReactiveFormsModule } from '@angular/forms';
import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-registration',
  templateUrl: './registration.component.html',
  styleUrls: ['./registration.component.css'],
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule]
})
export class RegistrationComponent implements OnInit {
  registrationForm: FormGroup;
  submitted = false;
  loading = false;
  registrationSuccess = false;

  constructor(
    private formBuilder: FormBuilder,
    private router: Router,
    private authService: AuthService
  ) {
    this.registrationForm = this.formBuilder.group({
      firstName: ['', [Validators.required, Validators.minLength(3)]],
      lastName: ['', [Validators.required, Validators.minLength(2)]],
      email: ['', [Validators.required, Validators.email]],
      phone: ['', [Validators.required, Validators.pattern('^[0-9]{10}$')]],
      address: ['', Validators.required],
      city: ['', Validators.required],
      state: ['', Validators.required],
      zipCode: ['', [Validators.required, Validators.pattern('^[0-9]{6}$')]],
      occupation: ['', Validators.required],
      dob: ['', Validators.required],
      password: ['', [
        Validators.required,
        Validators.minLength(8),
        Validators.pattern('(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&].{8,}')
      ]],
      confirmPassword: ['', Validators.required]
    }, { validator: this.passwordMatchValidator });
  }

  ngOnInit(): void {
  }

  get f() { return this.registrationForm.controls; }

  passwordMatchValidator(g: FormGroup) {
    return g.get('password')?.value === g.get('confirmPassword')?.value ? null : { mismatch: true };
  }

  onSubmit() {
    this.submitted = true;

    if (this.registrationForm.invalid) {
      // Mark all fields as touched to trigger validation display
      Object.keys(this.registrationForm.controls).forEach(key => {
        const control = this.registrationForm.get(key);
        control?.markAsTouched();
      });
      return;
    }

    this.loading = true;
    try {
      this.authService.register(this.registrationForm.value);
      setTimeout(() => {
        this.loading = false;
        this.registrationSuccess = true;
        setTimeout(() => {
          this.router.navigate(['/login'], {
            queryParams: {
              registered: 'true',
              email: this.registrationForm.value.email
            }
          });
        }, 1500);
      }, 1500);
    } catch (error) {
      this.loading = false;
      console.error('Registration failed:', error);
    }
  }

  onReset() {
    this.submitted = false;
    this.registrationForm.reset();
  }
}