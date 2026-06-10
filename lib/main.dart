import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'models/usuario.dart';
import 'services/api_service.dart';
import 'screens/login_screen.dart';
import 'screens/usuario_final/home_screen.dart';
import 'screens/estabelecimento/home_screen.dart';
import 'screens/funcionario/home_screen.dart';

void main() {
  runApp(const AgendaJaApp());
}

class AgendaJaApp extends StatelessWidget {
  const AgendaJaApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'AgendaJá',
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(
          seedColor: const Color(0xFF1565C0),
          primary: const Color(0xFF1565C0),
          secondary: const Color(0xFF42A5F5),
        ),
        useMaterial3: true,
        fontFamily: 'Roboto',
        appBarTheme: const AppBarTheme(
          backgroundColor: Color(0xFF1565C0),
          foregroundColor: Colors.white,
          elevation: 0,
        ),
        elevatedButtonTheme: ElevatedButtonThemeData(
          style: ElevatedButton.styleFrom(
            backgroundColor: const Color(0xFF1565C0),
            foregroundColor: Colors.white,
            padding: const EdgeInsets.symmetric(vertical: 16),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(12),
            ),
          ),
        ),
        inputDecorationTheme: InputDecorationTheme(
          border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(12),
          ),
          contentPadding:
              const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        ),
      ),
      home: const SplashScreen(),
    );
  }
}

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    _verificarLogin();
  }

  Future<void> _verificarLogin() async {
    await Future.delayed(const Duration(seconds: 1));
    final usuario = await ApiService.getUsuarioSalvo();

    if (!mounted) return;

    if (usuario != null) {
      _navegarParaHome(usuario);
    } else {
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (_) => const LoginScreen()),
      );
    }
  }

  void _navegarParaHome(Usuario usuario) {
    Widget home;
    if (usuario.isEstabelecimento) {
      home = EstabelecimentoHomeScreen(usuario: usuario);
    } else if (usuario.isFuncionario) {
      home = FuncionarioHomeScreen(usuario: usuario);
    } else {
      home = UsuarioFinalHomeScreen(usuario: usuario);
    }

    Navigator.pushReplacement(
      context,
      MaterialPageRoute(builder: (_) => home),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF1565C0),
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              width: 100,
              height: 100,
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(24),
              ),
              child: const Icon(
                Icons.calendar_month,
                size: 60,
                color: Color(0xFF1565C0),
              ),
            ),
            const SizedBox(height: 24),
            const Text(
              'AgendaJá',
              style: TextStyle(
                fontSize: 36,
                fontWeight: FontWeight.bold,
                color: Colors.white,
                letterSpacing: 1.2,
              ),
            ),
            const SizedBox(height: 8),
            const Text(
              'Seu agendamento simplificado',
              style: TextStyle(
                fontSize: 16,
                color: Colors.white70,
              ),
            ),
            const SizedBox(height: 48),
            const CircularProgressIndicator(
              valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
            ),
          ],
        ),
      ),
    );
  }
}
