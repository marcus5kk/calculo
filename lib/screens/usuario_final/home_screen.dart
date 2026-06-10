import 'package:flutter/material.dart';
import '../../models/usuario.dart';
import '../../services/api_service.dart';
import '../login_screen.dart';

class UsuarioFinalHomeScreen extends StatelessWidget {
  final Usuario usuario;

  const UsuarioFinalHomeScreen({super.key, required this.usuario});

  Future<void> _logout(BuildContext context) async {
    await ApiService.logout();
    if (!context.mounted) return;
    Navigator.pushReplacement(
      context,
      MaterialPageRoute(builder: (_) => const LoginScreen()),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[50],
      appBar: AppBar(
        title: const Text('AgendaJá'),
        actions: [
          IconButton(
            icon: const Icon(Icons.logout),
            tooltip: 'Sair',
            onPressed: () => _logout(context),
          ),
        ],
      ),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [Color(0xFF1565C0), Color(0xFF42A5F5)],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Icon(Icons.waving_hand, color: Colors.white70, size: 28),
                    const SizedBox(height: 8),
                    Text(
                      'Olá, ${usuario.nome.split(' ').first}!',
                      style: const TextStyle(
                        fontSize: 22,
                        fontWeight: FontWeight.bold,
                        color: Colors.white,
                      ),
                    ),
                    const SizedBox(height: 4),
                    const Text(
                      'O que você precisa agendar hoje?',
                      style: TextStyle(color: Colors.white70, fontSize: 14),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 28),
              const Text(
                'Serviços',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFF1565C0),
                ),
              ),
              const SizedBox(height: 16),
              Expanded(
                child: GridView.count(
                  crossAxisCount: 2,
                  crossAxisSpacing: 12,
                  mainAxisSpacing: 12,
                  children: [
                    _MenuCard(
                      icon: Icons.search,
                      label: 'Buscar Serviços',
                      sublabel: 'Em breve',
                      color: const Color(0xFF1565C0),
                      habilitado: false,
                    ),
                    _MenuCard(
                      icon: Icons.calendar_today,
                      label: 'Meus Agendamentos',
                      sublabel: 'Em breve',
                      color: const Color(0xFF0288D1),
                      habilitado: false,
                    ),
                    _MenuCard(
                      icon: Icons.history,
                      label: 'Histórico',
                      sublabel: 'Em breve',
                      color: const Color(0xFF0097A7),
                      habilitado: false,
                    ),
                    _MenuCard(
                      icon: Icons.person,
                      label: 'Meu Perfil',
                      sublabel: 'Em breve',
                      color: const Color(0xFF00796B),
                      habilitado: false,
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _MenuCard extends StatelessWidget {
  final IconData icon;
  final String label;
  final String sublabel;
  final Color color;
  final bool habilitado;

  const _MenuCard({
    required this.icon,
    required this.label,
    required this.sublabel,
    required this.color,
    required this.habilitado,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: habilitado
          ? null
          : () {
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(content: Text('Em breve disponível!')),
              );
            },
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.06),
              blurRadius: 8,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: color.withValues(alpha: 0.1),
                shape: BoxShape.circle,
              ),
              child: Icon(icon, color: color, size: 28),
            ),
            const SizedBox(height: 12),
            Text(
              label,
              textAlign: TextAlign.center,
              style: const TextStyle(
                fontWeight: FontWeight.bold,
                fontSize: 13,
              ),
            ),
            const SizedBox(height: 2),
            Text(
              sublabel,
              style: TextStyle(fontSize: 11, color: Colors.grey[500]),
            ),
          ],
        ),
      ),
    );
  }
}
