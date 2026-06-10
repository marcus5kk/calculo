import 'package:flutter/material.dart';
import '../models/usuario.dart';
import '../services/api_service.dart';
import 'register_screen.dart';
import 'usuario_final/home_screen.dart';
import 'estabelecimento/home_screen.dart';
import 'funcionario/home_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController();
  final _senhaController = TextEditingController();
  bool _carregando = false;
  bool _senhaVisivel = false;
  String? _erro;

  @override
  void dispose() {
    _emailController.dispose();
    _senhaController.dispose();
    super.dispose();
  }

  Future<void> _login() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      _carregando = true;
      _erro = null;
    });

    final resultado = await ApiService.login(
      _emailController.text.trim(),
      _senhaController.text,
    );

    if (!mounted) return;
    setState(() => _carregando = false);

    if (resultado['sucesso'] == true) {
      final usuario = Usuario.fromJson(resultado['usuario']);
      _navegarParaHome(usuario);
    } else {
      setState(() => _erro = resultado['mensagem'] ?? 'Erro ao fazer login.');
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
      backgroundColor: Colors.grey[50],
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              const SizedBox(height: 48),
              Center(
                child: Container(
                  width: 80,
                  height: 80,
                  decoration: BoxDecoration(
                    color: const Color(0xFF1565C0),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: const Icon(
                    Icons.calendar_month,
                    size: 48,
                    color: Colors.white,
                  ),
                ),
              ),
              const SizedBox(height: 16),
              const Center(
                child: Text(
                  'AgendaJá',
                  style: TextStyle(
                    fontSize: 28,
                    fontWeight: FontWeight.bold,
                    color: Color(0xFF1565C0),
                  ),
                ),
              ),
              const SizedBox(height: 4),
              const Center(
                child: Text(
                  'Entre na sua conta',
                  style: TextStyle(fontSize: 15, color: Colors.grey),
                ),
              ),
              const SizedBox(height: 40),
              Form(
                key: _formKey,
                child: Column(
                  children: [
                    TextFormField(
                      controller: _emailController,
                      keyboardType: TextInputType.emailAddress,
                      textInputAction: TextInputAction.next,
                      decoration: const InputDecoration(
                        labelText: 'E-mail',
                        prefixIcon: Icon(Icons.email_outlined),
                        filled: true,
                        fillColor: Colors.white,
                      ),
                      validator: (v) {
                        if (v == null || v.trim().isEmpty) {
                          return 'Informe o e-mail';
                        }
                        if (!v.contains('@')) return 'E-mail inválido';
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _senhaController,
                      obscureText: !_senhaVisivel,
                      textInputAction: TextInputAction.done,
                      onFieldSubmitted: (_) => _login(),
                      decoration: InputDecoration(
                        labelText: 'Senha',
                        prefixIcon: const Icon(Icons.lock_outlined),
                        filled: true,
                        fillColor: Colors.white,
                        suffixIcon: IconButton(
                          icon: Icon(_senhaVisivel
                              ? Icons.visibility_off
                              : Icons.visibility),
                          onPressed: () =>
                              setState(() => _senhaVisivel = !_senhaVisivel),
                        ),
                      ),
                      validator: (v) {
                        if (v == null || v.isEmpty) return 'Informe a senha';
                        if (v.length < 6) return 'Mínimo 6 caracteres';
                        return null;
                      },
                    ),
                    const SizedBox(height: 24),
                    if (_erro != null)
                      Container(
                        padding: const EdgeInsets.all(12),
                        margin: const EdgeInsets.only(bottom: 16),
                        decoration: BoxDecoration(
                          color: Colors.red[50],
                          border: Border.all(color: Colors.red[200]!),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Row(
                          children: [
                            const Icon(Icons.error_outline,
                                color: Colors.red, size: 18),
                            const SizedBox(width: 8),
                            Expanded(
                              child: Text(
                                _erro!,
                                style: const TextStyle(color: Colors.red),
                              ),
                            ),
                          ],
                        ),
                      ),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        onPressed: _carregando ? null : _login,
                        child: _carregando
                            ? const SizedBox(
                                height: 20,
                                width: 20,
                                child: CircularProgressIndicator(
                                  strokeWidth: 2,
                                  valueColor: AlwaysStoppedAnimation<Color>(
                                      Colors.white),
                                ),
                              )
                            : const Text(
                                'Entrar',
                                style: TextStyle(
                                    fontSize: 16, fontWeight: FontWeight.bold),
                              ),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 24),
              Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Text('Não tem conta? ',
                      style: TextStyle(color: Colors.grey)),
                  GestureDetector(
                    onTap: () => Navigator.push(
                      context,
                      MaterialPageRoute(
                          builder: (_) => const RegisterScreen()),
                    ),
                    child: const Text(
                      'Cadastre-se',
                      style: TextStyle(
                        color: Color(0xFF1565C0),
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 32),
            ],
          ),
        ),
      ),
    );
  }
}
