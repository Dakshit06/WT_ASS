export const testData = {
  validRegistration: {
    firstName: 'John',
    lastName: 'Doe',
    email: 'john.doe@example.com',
    phone: '1234567890',
    password: 'Test@123',
    confirmPassword: 'Test@123',
    address: '123 Main St',
    city: 'Mumbai',
    state: 'Maharashtra',
    zipCode: '400001',
    occupation: 'Developer',
    dob: '1990-01-01'
  },
  invalidInputs: {
    email: 'invalid-email',
    phone: '123',
    password: 'weak',
    zipCode: '12'
  }
};

export const testCases = [
  {
    name: 'Empty form submission',
    data: {},
    expectedErrors: ['firstName', 'lastName', 'email', 'phone', 'password']
  },
  {
    name: 'Invalid email format',
    data: { ...testData.validRegistration, email: 'invalid-email' },
    expectedErrors: ['email']
  },
  {
    name: 'Password mismatch',
    data: { ...testData.validRegistration, confirmPassword: 'Different@123' },
    expectedErrors: ['mismatch']
  }
];
