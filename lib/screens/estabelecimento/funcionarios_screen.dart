import 'package:flutter/material.dart';
import '../../models/usuario.dart';
import '../../services/api_service.dart';

class FuncionariosScreen extends StatefulWidget {
  final Usuario empresa;

  const FuncionariosScreen({super.key, required this.empresa});

  @override
  State<FuncionariosScreen> createState() => _FuncionariosScreenState();
}

class _FuncionariosScreenState extends State<FuncionariosScreen> {
  List<dynamic> _funcionarios = [];
  bool _carregando = true;

  @override
  void initState() {
    super.initState();
    _carregarFuncionarios();
  }

  Future<void> _carregarFuncionarios() async {
    setState(() => _carregando = true);
    final lista = await ApiService.getFuncionarios(widget.empresa.id);
    if (mounted) {
      setState(() {
        _funcionarios = lista;
        _carregando = false;
      });
    }
  }

  void _abrirDialogCriarFuncionario() {
    final nomeCtrl = TextEditingController();
    final emailCtrl = TextEditingController();
    final senhaCtrl = TextEditingController();
    final formKey = GlobalKey<FormState>();
    bool salvando = false;

    showDialog(
      context: context,
      builder: (ctx) {
        return StatefulBuilder(
          builder: (ctx, setStateDialog) {
            return AlertDialog(
              title: const Row(
                children: [
                  Icon(Icons.person_add, color: Color(0xFF2E7D32)),
                  SizedBox(width: 8),
                  Text('Novo Funcionário'),
                ],
              ),
              content: Form(
                key: formKey,
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    TextFormField(
                      controller: nomeCtrl,
                      textCapitalization: TextCapitalization.words,
                      decoration: const InputDecoration(
                        labelText: 'Nome completo',
                        prefixIcon: Icon(Icons.person_outlined),
                      ),
                      validator: (v) {
                        if (v == null || v.trim().isEmpty) {
                          return 'Informe o nome';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 12),
                    TextFormField(
                      controller: emailCtrl,
                      keyboardType: TextInputType.emailAddress,
                      decoration: const InputDecoration(
                        labelText: 'E-mail',
                        prefixIcon: Icon(Icons.email_outlined),
                      ),
                      validator: (v) {
                        if (v == null || v.trim().isEmpty) {
                          return 'Informe o e-mail';
                        }
                        if (!v.contains('@')) return 'E-mail inválido';
                        return null;
                      },
                    ),
                    const SizedBox(height: 12),
                    TextFormField(
                      controller: senhaCtrl,
                      obscureText: true,
                      decoration: const InputDecoration(
                        labelText: 'Senha inicial',
                        prefixIcon: Icon(Icons.lock_outlined),
                      ),
                      validator: (v) {
                        if (v == null || v.isEmpty) return 'Informe a senha';
                        if (v.length < 6) return 'Mínimo 6 caracteres';
                        return null;
                      },
                    ),
                  ],
                ),
              ),
              actions: [
                TextButton(
                  onPressed: salvando ? null : () => Navigator.pop(ctx),
                  child: const Text('Cancelar'),
                ),
                ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF2E7D32),
                  ),
                  onPressed: salvando
                      ? null
                      : () async {
                          if (!formKey.currentState!.validate()) return;
                          setStateDialog(() => salvando = true);

                          final resultado = await ApiService.criarFuncionario(
                            nomeCtrl.text.trim(),
                            emailCtrl.text.trim(),
                            senhaCtrl.text,
                            widget.empresa.id,
                          );

                          if (!ctx.mounted) return;
                          Navigator.pop(ctx);

                          if (resultado['sucesso'] == true) {
                            if (!mounted) return;
                            ScaffoldMessenger.of(context).showSnackBar(
                              const SnackBar(
                                content:
                                    Text('Funcionário cadastrado com sucesso!'),
                                backgroundColor: Colors.green,
                              ),
                            );
                            _carregarFuncionarios();
                          } else {
                            if (!mounted) return;
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(
                                content: Text(resultado['mensagem'] ??
                                    'Erro ao criar funcionário.'),
                                backgroundColor: Colors.red,
                              ),
                            );
                          }
                        },
                  child: salvando
                      ? const SizedBox(
                          width: 18,
                          height: 18,
                          child: CircularProgressIndicator(
                            strokeWidth: 2,
                            valueColor:
                                AlwaysStoppedAnimation<Color>(Colors.white),
                          ),
                        )
                      : const Text('Cadastrar'),
                ),
              ],
            );
          },
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[50],
      appBar: AppBar(
        title: const Text('Funcionários'),
        backgroundColor: const Color(0xFF2E7D32),
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: _abrirDialogCriarFuncionario,
        backgroundColor: const Color(0xFF2E7D32),
        icon: const Icon(Icons.person_add),
        label: const Text('Novo Funcionário'),
      ),
      body: _carregando
          ? const Center(child: CircularProgressIndicator())
          : _funcionarios.isEmpty
              ? Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.group_off,
                          size: 64, color: Colors.grey[300]),
                      const SizedBox(height: 16),
                      const Text(
                        'Nenhum funcionário cadastrado',
                        style: TextStyle(fontSize: 16, color: Colors.grey),
                      ),
                      const SizedBox(height: 8),
                      const Text(
                        'Toque no botão + para adicionar',
                        style: TextStyle(fontSize: 13, color: Colors.grey),
                      ),
                    ],
                  ),
                )
              : RefreshIndicator(
                  onRefresh: _carregarFuncionarios,
                  child: ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: _funcionarios.length,
                    itemBuilder: (context, index) {
                      final f = _funcionarios[index];
                      return Container(
                        margin: const EdgeInsets.only(bottom: 12),
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(12),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withValues(alpha: 0.05),
                              blurRadius: 6,
                              offset: const Offset(0, 2),
                            ),
                          ],
                        ),
                        child: Row(
                          children: [
                            CircleAvatar(
                              backgroundColor:
                                  const Color(0xFF2E7D32).withValues(alpha: 0.1),
                              child: Text(
                                (f['nome'] as String? ?? '?')
                                    .substring(0, 1)
                                    .toUpperCase(),
                                style: const TextStyle(
                                  color: Color(0xFF2E7D32),
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    f['nome'] ?? '',
                                    style: const TextStyle(
                                      fontWeight: FontWeight.bold,
                                      fontSize: 15,
                                    ),
                                  ),
                                  const SizedBox(height: 2),
                                  Text(
                                    f['email'] ?? '',
                                    style: TextStyle(
                                      color: Colors.grey[600],
                                      fontSize: 13,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                            Container(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 8, vertical: 4),
                              decoration: BoxDecoration(
                                color: Colors.green[50],
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: const Text(
                                'Ativo',
                                style: TextStyle(
                                  color: Color(0xFF2E7D32),
                                  fontSize: 12,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
                          ],
                        ),
                      );
                    },
                  ),
                ),
    );
  }
}
