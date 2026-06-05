import 'package:flutter/material.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'Calculadora',
      theme: ThemeData(
        primarySwatch: Colors.blue,
      ),
      home: const CalculatorPage(),
    );
  }
}

class CalculatorPage extends StatefulWidget {
  const CalculatorPage({super.key});

  @override
  State<CalculatorPage> createState() => _CalculatorPageState();
}

class _CalculatorPageState extends State<CalculatorPage> {
  String display = '0';
  double firstNumber = 0;
  String operation = '';

  void press(String value) {
    setState(() {
      if (value == 'C') {
        display = '0';
        firstNumber = 0;
        operation = '';
      } else if (['+', '-', '×', '÷'].contains(value)) {
        firstNumber = double.parse(display);
        operation = value;
        display = '0';
      } else if (value == '=') {
        double secondNumber = double.parse(display);
        double result = 0;

        switch (operation) {
          case '+':
            result = firstNumber + secondNumber;
            break;
          case '-':
            result = firstNumber - secondNumber;
            break;
          case '×':
            result = firstNumber * secondNumber;
            break;
          case '÷':
            result = secondNumber != 0
                ? firstNumber / secondNumber
                : 0;
            break;
        }

        display = result.toString();
      } else {
        display = display == '0' ? value : display + value;
      }
    });
  }

  Widget button(String text) {
    return Expanded(
      child: Padding(
        padding: const EdgeInsets.all(4),
        child: ElevatedButton(
          onPressed: () => press(text),
          child: Text(
            text,
            style: const TextStyle(fontSize: 24),
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Calculadora'),
      ),
      body: Column(
        children: [
          Expanded(
            child: Container(
              alignment: Alignment.bottomRight,
              padding: const EdgeInsets.all(20),
              child: Text(
                display,
                style: const TextStyle(
                  fontSize: 48,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
          ),
          Row(children: [button('7'), button('8'), button('9'), button('÷')]),
          Row(children: [button('4'), button('5'), button('6'), button('×')]),
          Row(children: [button('1'), button('2'), button('3'), button('-')]),
          Row(children: [button('C'), button('0'), button('='), button('+')]),
        ],
      ),
    );
  }
}