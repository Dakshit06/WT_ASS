import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private isAuthenticatedSubject = new BehaviorSubject<boolean>(false);

  login(email: string, password: string): boolean {
    try {
      const userData = JSON.parse(localStorage.getItem('userData') || '{}');
      const isValid = userData.email === email && userData.password === password;
      
      if (isValid) {
        this.isAuthenticatedSubject.next(true);
        localStorage.setItem('isLoggedIn', 'true');
      }
      
      return isValid;
    } catch (error) {
      console.error('Login error:', error);
      return false;
    }
  }

  register(userData: any): void {
    try {
      if (!userData.email || !userData.password) {
        throw new Error('Invalid registration data');
      }
      localStorage.setItem('userData', JSON.stringify(userData));
    } catch (error) {
      console.error('Registration error:', error);
      throw error;
    }
  }

  logout(): void {
    this.isAuthenticatedSubject.next(false);
    localStorage.removeItem('isLoggedIn');
  }

  isAuthenticated(): Observable<boolean> {
    return this.isAuthenticatedSubject.asObservable();
  }
}
