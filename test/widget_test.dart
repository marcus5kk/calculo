import 'package:flutter_test/flutter_test.dart';
import 'package:agendaja/main.dart';

void main() {
  testWidgets('AgendaJá smoke test', (WidgetTester tester) async {
    await tester.pumpWidget(const AgendaJaApp());
    expect(find.text('AgendaJá'), findsWidgets);
  });
}
