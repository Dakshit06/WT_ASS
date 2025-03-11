import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-forgot-password',
  templateUrl: './forgot-password.component.html',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  styles: [`
    :host {
      display: block;
      padding: 1rem;
      background-color: #f8f9fa;
      min-height: 100vh;
    }
    
    .alert {
      margin-bottom: 1.5rem;
    }
    
    .form-control:focus {
      background-color: #fff;
    }
    
    @media (max-width: 768px) {
      :host {
        padding: 0.5rem;
      }
    }
  `]
})
export class ForgotPasswordComponent {
  resetForm: FormGroup;
  submitted = false;
  loading = false;
  resetSuccess = false;
  resetError = '';

  constructor(private formBuilder: FormBuilder) {
    this.resetForm = this.formBuilder.group({
      email: ['', [Validators.required, Validators.email]]
    });
  }

  onSubmit() {
    this.submitted = true;
    this.resetError = '';
    this.resetSuccess = false;

    if (this.resetForm.invalid) {
      Object.keys(this.resetForm.controls).forEach(key => {
        this.resetForm.get(key)?.markAsTouched();
      });
      return;
    }

    this.loading = true;
    // Simulate API call
    setTimeout(() => {
      try {
        // Simulated success
        this.loading = false;
        this.resetSuccess = true;
        this.resetError = '';
        this.resetForm.reset();
        this.submitted = false;
      } catch (error) {
        this.loading = false;
        this.resetSuccess = false;
        this.resetError = 'Failed to send reset link. Please try again.';
        console.error('Reset password error:', error);
      }
    }, 1500);
  }
}
