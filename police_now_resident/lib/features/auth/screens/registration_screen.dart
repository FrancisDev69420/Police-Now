import 'package:flutter/material.dart';
import '../../../core/widgets/custom_text_field.dart';
import '../../../core/utils/validators.dart';

class RegistrationScreen extends StatefulWidget {
  const RegistrationScreen({Key? key}) : super(key: key);

  @override
  _RegistrationScreenState createState() => _RegistrationScreenState();
}

class _RegistrationScreenState extends State<RegistrationScreen> {
  final PageController _pageController = PageController();
  int _currentStep = 0;
  
  // Form controllers
  final _personalFormKey = GlobalKey<FormState>();
  final _contactFormKey = GlobalKey<FormState>();
  final _locationFormKey = GlobalKey<FormState>();
  
  final _firstNameController = TextEditingController();
  final _lastNameController = TextEditingController();
  final _emailController = TextEditingController();
  final _phoneController = TextEditingController();
  final _passwordController = TextEditingController();
  
  @override
  void dispose() {
    _pageController.dispose();
    _firstNameController.dispose();
    _lastNameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Create Account'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: _currentStep > 0 ? _previousStep : () => Navigator.pop(context),
        ),
      ),
      body: Column(
        children: [
          // Progress Indicator
          LinearProgressIndicator(
            value: (_currentStep + 1) / 4,
            backgroundColor: Colors.grey[300],
          ),
          
          // Form Steps
          Expanded(
            child: PageView(
              controller: _pageController,
              physics: const NeverScrollableScrollPhysics(),
              children: [
                _buildPersonalDetailsStep(),
                _buildContactDetailsStep(),
                _buildLocationStep(),
                _buildVerificationStep(),
              ],
            ),
          ),
          
          // Navigation Buttons
          Padding(
            padding: const EdgeInsets.all(20),
            child: Row(
              children: [
                if (_currentStep > 0)
                  Expanded(
                    child: OutlinedButton(
                      onPressed: _previousStep,
                      child: const Text('Previous'),
                    ),
                  ),
                if (_currentStep > 0) const SizedBox(width: 16),
                Expanded(
                  child: ElevatedButton(
                    onPressed: _currentStep < 3 ? _nextStep : _completeRegistration,
                    child: Text(_currentStep < 3 ? 'Next' : 'Complete'),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPersonalDetailsStep() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(20),
      child: Form(
        key: _personalFormKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Personal Information', 
                 style: Theme.of(context).textTheme.headlineSmall),
            const SizedBox(height: 20),
            
            CustomTextField(
              controller: _firstNameController,
              labelText: 'First Name',
              validator: Validators.required,
            ),
            const SizedBox(height: 16),
            
            CustomTextField(
              controller: _lastNameController,
              labelText: 'Last Name',
              validator: Validators.required,
            ),
            const SizedBox(height: 16),
            
            CustomTextField(
              controller: _emailController,
              labelText: 'Email',
              keyboardType: TextInputType.emailAddress,
              validator: Validators.email,
            ),
            const SizedBox(height: 16),
            
            CustomTextField(
              controller: _phoneController,
              labelText: 'Phone Number',
              keyboardType: TextInputType.phone,
              validator: Validators.phone,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildContactDetailsStep() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(20),
      child: Form(
        key: _contactFormKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Contact Information',
                style: Theme.of(context).textTheme.headlineSmall),
            const SizedBox(height: 20),
            
            CustomTextField(
              controller: _passwordController,
              labelText: 'Password',
              obscureText: true,
              validator: Validators.password,
            ),
            const SizedBox(height: 16),
            
            // Add password confirmation field
            CustomTextField(
              labelText: 'Confirm Password',
              obscureText: true,
              validator: (value) {
                if (value != _passwordController.text) {
                  return 'Passwords do not match';
                }
                return null;
              },
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildLocationStep() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(20),
      child: Form(
        key: _locationFormKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Location Settings',
                style: Theme.of(context).textTheme.headlineSmall),
            const SizedBox(height: 20),
            
            // Add location fields here
            const Text('Location settings will be implemented here'),
          ],
        ),
      ),
    );
  }

  Widget _buildVerificationStep() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text('Verify Your Information',
              style: Theme.of(context).textTheme.headlineSmall),
          const SizedBox(height: 20),
          
          // Display summary of entered information
          const Text('Please review your information before completing registration.'),
        ],
      ),
    );
  }

  void _nextStep() {
    if (_validateCurrentStep()) {
      if (_currentStep < 3) {
        _pageController.nextPage(
          duration: const Duration(milliseconds: 300),
          curve: Curves.easeInOut,
        );
        setState(() => _currentStep++);
      }
    }
  }

  void _previousStep() {
    if (_currentStep > 0) {
      _pageController.previousPage(
        duration: const Duration(milliseconds: 300),
        curve: Curves.easeInOut,
      );
      setState(() => _currentStep--);
    }
  }

  bool _validateCurrentStep() {
    switch (_currentStep) {
      case 0:
        return _personalFormKey.currentState!.validate();
      case 1:
        return _contactFormKey.currentState!.validate();
      case 2:
        return _locationFormKey.currentState!.validate();
      default:
        return true;
    }
  }

  void _completeRegistration() {
    // TODO: Implement registration completion
  }
} 