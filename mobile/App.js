import AsyncStorage from '@react-native-async-storage/async-storage';
import { StatusBar } from 'expo-status-bar';
import React, { useEffect, useMemo, useState } from 'react';
import QRCode from 'react-native-qrcode-svg';
import {
  ActivityIndicator,
  Alert,
  Image,
  ImageBackground,
  Pressable,
  SafeAreaView,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  View,
} from 'react-native';

const API_BASE_URL = 'http://192.168.2.23:8010/api/mobile';

const logo = require('./assets/logo-header.png');
const poster = require('./assets/appmovil.png');

const demoTravels = [
  { id: 'demo-1', demo: true, origin: 'Ciudad A', destination: 'Ciudad B', date_label: '24/05/2024', time: '08:30', price: 15, seats: sampleSeats() },
  { id: 'demo-2', demo: true, origin: 'Ciudad A', destination: 'Ciudad C', date_label: '24/05/2024', time: '10:30', price: 15, seats: sampleSeats() },
  { id: 'demo-3', demo: true, origin: 'Ciudad A', destination: 'Ciudad D', date_label: '24/05/2024', time: '13:00', price: 15, seats: sampleSeats() },
];

function sampleSeats() {
  return Array.from({ length: 24 }, (_, index) => ({
    id: index + 1,
    number: String(index + 1).padStart(2, '0'),
    price: 15,
    occupied: [3, 6, 11, 17, 22].includes(index + 1),
  }));
}

export default function App() {
  const [token, setToken] = useState(null);
  const [user, setUser] = useState(null);
  const [tab, setTab] = useState('inicio');
  const [loading, setLoading] = useState(true);
  const [travels, setTravels] = useState([]);
  const [tickets, setTickets] = useState([]);
  const [encomiendas, setEncomiendas] = useState([]);

  const api = useMemo(() => createApi(token), [token]);

  useEffect(() => {
    restoreSession();
  }, []);

  useEffect(() => {
    if (token) refreshData();
  }, [token]);

  async function restoreSession() {
    const savedToken = await AsyncStorage.getItem('cuchao_token');
    const savedUser = await AsyncStorage.getItem('cuchao_user');
    setToken(savedToken);
    setUser(savedUser ? JSON.parse(savedUser) : null);
    setLoading(false);
  }

  async function saveSession(nextToken, nextUser) {
    await AsyncStorage.setItem('cuchao_token', nextToken);
    await AsyncStorage.setItem('cuchao_user', JSON.stringify(nextUser));
    setToken(nextToken);
    setUser(nextUser);
  }

  async function refreshData() {
    const [travelResult, ticketResult, packageResult] = await Promise.allSettled([
      api.get('/travels'),
      api.get('/tickets'),
      api.get('/encomiendas'),
    ]);

    setTravels(travelResult.status === 'fulfilled' ? travelResult.value.data : demoTravels);
    setTickets(ticketResult.status === 'fulfilled' ? ticketResult.value.data : []);
    setEncomiendas(packageResult.status === 'fulfilled' ? packageResult.value.data : []);
  }

  async function logout() {
    await api.post('/logout').catch(() => null);
    await AsyncStorage.multiRemove(['cuchao_token', 'cuchao_user']);
    setToken(null);
    setUser(null);
    setTickets([]);
    setTab('inicio');
  }

  if (loading) {
    return <LoadingScreen />;
  }

  if (!token) {
    return <AuthScreen onSession={saveSession} />;
  }

  const visibleTravels = travels.length ? travels : demoTravels;

  return (
    <SafeAreaView style={styles.safe}>
      <StatusBar style="light" />
      <View style={styles.app}>
        <Header user={user} />

        {tab === 'inicio' && <HomeScreen travels={visibleTravels} user={user} onTab={setTab} />}
        {tab === 'comprar' && <BuyScreen api={api} travels={visibleTravels} user={user} onBought={(ticket) => {
          setTickets((current) => [ticket, ...current]);
          setTab('boletos');
        }} />}
        {tab === 'pagar' && <PayScreen api={api} tickets={tickets} onPaid={refreshData} />}
        {tab === 'boletos' && <TicketsScreen tickets={tickets} />}
        {tab === 'encomiendas' && <PackagesScreen encomiendas={encomiendas} />}
        {tab === 'perfil' && <ProfileScreen user={user} onLogout={logout} />}

        <BottomNav tab={tab} setTab={setTab} />
      </View>
    </SafeAreaView>
  );
}

function createApi(token) {
  async function request(path, options = {}) {
    const response = await fetch(`${API_BASE_URL}${path}`, {
      ...options,
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        ...(token ? { Authorization: `Bearer ${token}` } : {}),
        ...(options.headers || {}),
      },
    });

    const body = await response.json().catch(() => ({}));
    if (!response.ok) {
      const error = new Error(body.message || 'No se pudo conectar con el servidor.');
      error.data = body;
      throw error;
    }
    return body;
  }

  return {
    get: (path) => request(path),
    post: (path, data) => request(path, { method: 'POST', body: JSON.stringify(data) }),
    put: (path, data) => request(path, { method: 'PUT', body: JSON.stringify(data) }),
  };
}

function LoadingScreen() {
  return (
    <View style={[styles.safe, styles.center]}>
      <ActivityIndicator color="#ff7900" size="large" />
    </View>
  );
}

function AuthScreen({ onSession }) {
  const [mode, setMode] = useState('login');
  const [form, setForm] = useState({ name: '', email: '', password: '', cedula: '', telefono: '' });
  const [busy, setBusy] = useState(false);
  const api = useMemo(() => createApi(null), []);

  async function submit() {
    setBusy(true);
    try {
      const path = mode === 'login' ? '/login' : '/register';
      const payload = mode === 'login'
        ? { email: form.email, password: form.password }
        : form;
      const result = await api.post(path, payload);
      await onSession(result.token, result.user);
    } catch (error) {
      Alert.alert('Cuchao', error.message);
    } finally {
      setBusy(false);
    }
  }

  return (
    <ImageBackground source={poster} style={styles.authBg} imageStyle={styles.authBgImage}>
      <SafeAreaView style={styles.authOverlay}>
        <Image source={logo} style={styles.authLogo} resizeMode="contain" />
        <Text style={styles.authTitle}>Tu viaje, más fácil.</Text>
        <View style={styles.authCard}>
          <Text style={styles.sectionTitle}>{mode === 'login' ? 'Ingresar' : 'Crear cuenta'}</Text>
          {mode === 'register' && (
            <>
              <Field placeholder="Nombre" value={form.name} onChangeText={(name) => setForm({ ...form, name })} />
              <Field placeholder="Cedula" value={form.cedula} onChangeText={(cedula) => setForm({ ...form, cedula })} />
              <Field placeholder="Telefono" value={form.telefono} onChangeText={(telefono) => setForm({ ...form, telefono })} />
            </>
          )}
          <Field placeholder="Correo" value={form.email} onChangeText={(email) => setForm({ ...form, email })} />
          <Field placeholder="Contrasena" secureTextEntry value={form.password} onChangeText={(password) => setForm({ ...form, password })} />
          <PrimaryButton title={busy ? 'Conectando...' : mode === 'login' ? 'Iniciar sesión' : 'Registrarse'} onPress={submit} disabled={busy} />
          <Pressable onPress={() => setMode(mode === 'login' ? 'register' : 'login')}>
            <Text style={styles.link}>{mode === 'login' ? 'Crear una cuenta nueva' : 'Ya tengo cuenta'}</Text>
          </Pressable>
        </View>
      </SafeAreaView>
    </ImageBackground>
  );
}

function Header({ user }) {
  return (
    <View style={styles.header}>
      <Image source={logo} style={styles.logo} resizeMode="contain" />
      <Text style={styles.greeting}>Hola, {firstName(user?.name) || 'viajero'}</Text>
    </View>
  );
}

function HomeScreen({ travels, user, onTab }) {
  return (
    <ScrollView contentContainerStyle={styles.content}>
      <View style={styles.searchHero}>
        <Text style={styles.heroText}>¿A dónde vamos hoy?</Text>
        <PrimaryButton title="Buscar viaje" onPress={() => onTab('comprar')} />
      </View>
      <Section title="Rutas cercanas" action="Ver todas">
        {travels.slice(0, 3).map((travel) => <TravelRow key={travel.id} travel={travel} />)}
      </Section>
      <Section title="Acciones rápidas">
        <View style={styles.quickGrid}>
          <Quick title="Mis boletos" icon="▣" onPress={() => onTab('boletos')} />
          <Quick title="Pagos" icon="$" onPress={() => onTab('pagar')} />
          <Quick title="Encomiendas" icon="□" onPress={() => onTab('encomiendas')} />
          <Quick title="Perfil" icon="○" onPress={() => onTab('perfil')} />
        </View>
      </Section>
      <View style={styles.noticeRow}>
        <InfoPill title="Pago seguro" text="Transacciones protegidas" />
        <InfoPill title="Viaje en vivo" text="Sigue tu bus en tiempo real" />
      </View>
    </ScrollView>
  );
}

function BuyScreen({ api, travels, user, onBought }) {
  const [selectedId, setSelectedId] = useState(travels[0]?.id);
  const [seat, setSeat] = useState(null);
  const [busy, setBusy] = useState(false);
  const [freshTravel, setFreshTravel] = useState(null);
  const selected = travels.find((travel) => String(travel.id) === String(selectedId)) || travels[0];
  const displayTravel = freshTravel && String(freshTravel.id) === String(selected?.id) ? freshTravel : selected;
  const seats = displayTravel?.seats || [];
  const availableSeatsCount = seats.filter((item) => !item.occupied).length;

  useEffect(() => {
    if (!travels.length) return;

    const selectedStillExists = travels.some((travel) => String(travel.id) === String(selectedId));
    if (!selectedStillExists) {
      setSelectedId(travels[0].id);
      setSeat(null);
      setFreshTravel(null);
    }
  }, [travels, selectedId]);

  async function buy() {
    if (!selected || !seat) {
      Alert.alert('Cuchao', 'Selecciona un viaje y un asiento.');
      return;
    }

    if (selected.demo) {
      Alert.alert('Cuchao', 'No se pudo conectar con el servidor. Reinicia Laravel y recarga la app para comprar boletos reales.');
      return;
    }

    setBusy(true);
    try {
      const latest = await api.get(`/travels/${selected.id}`);
      const latestTravel = latest.data;
      const latestSeat = latestTravel.seats?.find((item) => String(item.id) === String(seat.id));

      setFreshTravel(latestTravel);

      if (!latestSeat) {
        setSeat(null);
        Alert.alert('Cuchao', 'Ese asiento ya no esta disponible para este viaje.');
        return;
      }

      if (latestSeat.occupied) {
        setSeat(null);
        Alert.alert('Cuchao', 'Ese asiento acaba de ocuparse. Actualice la vista y elige otro asiento libre.');
        return;
      }

      const result = await api.post('/tickets', {
        salida_id: selected.id,
        asiento_id: latestSeat.id,
        asiento_numero: latestSeat.number,
        pasajero_nombre: user?.name || 'Pasajero',
        pasajero_cedula: user?.cedula || '0000000000',
        tipo_descuento: 'ninguno',
        metodo_pago: 'tarjeta',
      });
      onBought(result.data);
    } catch (error) {
      if (error.data?.travel) {
        setFreshTravel(error.data.travel);
        setSeat(null);
      }
      Alert.alert('Cuchao', error.message);
    } finally {
      setBusy(false);
    }
  }

  return (
    <ScrollView contentContainerStyle={styles.content}>
      <Text style={styles.screenTitle}>Buscar viaje</Text>
      {travels.map((travel) => (
        <Pressable key={travel.id} onPress={() => { setSelectedId(travel.id); setSeat(null); setFreshTravel(null); }} style={[styles.scheduleCard, String(selected?.id) === String(travel.id) && styles.selectedCard]}>
          <View>
            <Text style={styles.scheduleTime}>{travel.time}</Text>
            <Text style={styles.muted}>{travel.origin} → {travel.destination}</Text>
            <Text style={styles.muted}>{travel.date_label}</Text>
          </View>
          <Text style={styles.price}>${Number(travel.price || 0).toFixed(2)}</Text>
        </Pressable>
      ))}
      <Section title="Vista previa de asientos">
        <Text style={styles.muted}>{availableSeatsCount} asientos disponibles. Los grises ya estan ocupados.</Text>
        <View style={styles.seatGrid}>
          {seats.map((item) => (
            <Pressable
              key={item.id}
              disabled={item.occupied}
              onPress={() => setSeat(item)}
              style={[
                styles.seat,
                item.occupied && styles.seatOccupied,
                seat?.id === item.id && styles.seatSelected,
              ]}
            >
              <Text style={styles.seatText}>{item.number}</Text>
            </Pressable>
          ))}
        </View>
      </Section>
      <PrimaryButton title={busy ? 'Reservando...' : 'Comprar boleto'} onPress={buy} disabled={busy} />
    </ScrollView>
  );
}

function PayScreen({ api, tickets, onPaid }) {
  const pending = tickets.find((ticket) => ticket.status !== 'pagado') || tickets[0];
  const [reference, setReference] = useState('');
  const [busy, setBusy] = useState(false);

  async function pay() {
    if (!pending) {
      Alert.alert('Cuchao', 'No tienes boletos pendientes.');
      return;
    }
    setBusy(true);
    try {
      await api.post(`/tickets/${pending.id}/payments`, { metodo: 'tarjeta', referencia: reference || 'APP-MOVIL' });
      await onPaid();
      Alert.alert('Cuchao', 'Pago enviado para validación.');
    } catch (error) {
      Alert.alert('Cuchao', error.message);
    } finally {
      setBusy(false);
    }
  }

  return (
    <ScrollView contentContainerStyle={styles.content}>
      <Text style={styles.screenTitle}>Pagar</Text>
      {pending ? <TicketCard ticket={pending} /> : <Empty text="No tienes boletos para pagar." />}
      <Field placeholder="Referencia o autorización" value={reference} onChangeText={setReference} />
      <PrimaryButton title={busy ? 'Enviando...' : 'Pagar con tarjeta'} onPress={pay} disabled={busy} />
    </ScrollView>
  );
}

function TicketsScreen({ tickets }) {
  return (
    <ScrollView contentContainerStyle={styles.content}>
      <Text style={styles.screenTitle}>Mis boletos</Text>
      {tickets.length ? tickets.map((ticket) => <TicketCard key={ticket.id} ticket={ticket} />) : <Empty text="Aun no tienes boletos." />}
    </ScrollView>
  );
}

function PackagesScreen({ encomiendas }) {
  return (
    <ScrollView contentContainerStyle={styles.content}>
      <Text style={styles.screenTitle}>Encomiendas</Text>
      {encomiendas.length ? encomiendas.map((item) => (
        <View key={item.id} style={styles.card}><Text style={styles.cardTitle}>{item.codigo}</Text></View>
      )) : <Empty text="No tienes encomiendas registradas." />}
    </ScrollView>
  );
}

function ProfileScreen({ user, onLogout }) {
  return (
    <ScrollView contentContainerStyle={styles.content}>
      <Text style={styles.screenTitle}>Perfil</Text>
      <View style={styles.card}>
        <Text style={styles.cardTitle}>{user?.name}</Text>
        <Text style={styles.muted}>{user?.email}</Text>
        <Text style={styles.muted}>Cedula: {user?.cedula || 'Sin registrar'}</Text>
        <Text style={styles.muted}>Telefono: {user?.telefono || 'Sin registrar'}</Text>
      </View>
      <PrimaryButton title="Cerrar sesión" onPress={onLogout} />
    </ScrollView>
  );
}

function BottomNav({ tab, setTab }) {
  const items = [
    ['inicio', 'Inicio', '⌂'],
    ['comprar', 'Comprar', '⌕'],
    ['pagar', 'Pagar', '$'],
    ['boletos', 'Boletos', '▣'],
    ['encomiendas', 'Encomiendas', '□'],
    ['perfil', 'Perfil', '○'],
  ];

  return (
    <View style={styles.bottomNav}>
      {items.map(([key, label, icon]) => (
        <Pressable key={key} onPress={() => setTab(key)} style={styles.navItem}>
          <Text style={[styles.navIcon, tab === key && styles.navActive]}>{icon}</Text>
          <Text style={[styles.navLabel, tab === key && styles.navActive]}>{label}</Text>
        </Pressable>
      ))}
    </View>
  );
}

function Section({ title, action, children }) {
  return (
    <View style={styles.section}>
      <View style={styles.sectionHeader}>
        <Text style={styles.sectionTitle}>{title}</Text>
        {action ? <Text style={styles.actionText}>{action}</Text> : null}
      </View>
      {children}
    </View>
  );
}

function TravelRow({ travel }) {
  return (
    <View style={styles.travelRow}>
      <View style={styles.pin}><Text style={styles.pinText}>⌖</Text></View>
      <View style={styles.travelInfo}>
        <Text style={styles.cardTitle}>{travel.origin} - {travel.destination}</Text>
        <Text style={styles.muted}>Próximo bus: {travel.time}</Text>
      </View>
      <Text style={styles.badge}>${Number(travel.price || 0).toFixed(2)}</Text>
    </View>
  );
}

function TicketCard({ ticket }) {
  const qrEnabled = (ticket.status === 'pagado' || ticket.payment?.status === 'validado') && ticket.qr_value;

  return (
    <View style={styles.ticket}>
      <View style={styles.ticketTop}>
        <Text style={styles.ticketCode}>{ticket.code}</Text>
        <Text style={styles.busBadge}>▣</Text>
      </View>
      <Text style={styles.status}>{ticket.status}</Text>
      <View style={styles.ticketGrid}>
        <Pair label="Origen" value={ticket.origin} />
        <Pair label="Destino" value={ticket.destination} />
        <Pair label="Fecha" value={ticket.date_label} />
        <Pair label="Hora" value={ticket.time} />
        <Pair label="Asiento" value={ticket.seat} />
        <Pair label="Pasajero" value={ticket.passenger_name} />
      </View>
      {qrEnabled ? (
        <View style={styles.qrMock}>
          <QRCode value={ticket.qr_value} size={150} backgroundColor="#ffffff" color="#111827" />
          <Text style={styles.qrHint}>Muestra este código al abordar</Text>
        </View>
      ) : (
        <View style={styles.qrPending}>
          <Text style={styles.pendingTitle}>QR pendiente</Text>
          <Text style={styles.muted}>El código de abordaje se genera cuando el administrador valida tu pago.</Text>
        </View>
      )}
    </View>
  );
}

function Pair({ label, value }) {
  return (
    <View style={styles.pair}>
      <Text style={styles.label}>{label}</Text>
      <Text style={styles.value}>{value || '-'}</Text>
    </View>
  );
}

function Quick({ title, icon, onPress }) {
  return (
    <Pressable style={styles.quick} onPress={onPress}>
      <Text style={styles.quickIcon}>{icon}</Text>
      <Text style={styles.quickText}>{title}</Text>
    </Pressable>
  );
}

function InfoPill({ title, text }) {
  return (
    <View style={styles.infoPill}>
      <Text style={styles.infoTitle}>{title}</Text>
      <Text style={styles.muted}>{text}</Text>
    </View>
  );
}

function Empty({ text }) {
  return <View style={styles.card}><Text style={styles.muted}>{text}</Text></View>;
}

function Field(props) {
  return <TextInput placeholderTextColor="#778195" style={styles.input} autoCapitalize="none" {...props} />;
}

function PrimaryButton({ title, onPress, disabled }) {
  return (
    <Pressable onPress={onPress} disabled={disabled} style={[styles.primaryButton, disabled && styles.disabled]}>
      <Text style={styles.primaryButtonText}>{title}</Text>
    </Pressable>
  );
}

function firstName(name) {
  return name?.split(' ')[0];
}

const styles = StyleSheet.create({
  safe: { flex: 1, backgroundColor: '#020713' },
  center: { alignItems: 'center', justifyContent: 'center' },
  app: { flex: 1, backgroundColor: '#020713' },
  header: { paddingHorizontal: 20, paddingTop: 16, paddingBottom: 8 },
  logo: { width: 170, height: 58, alignSelf: 'center' },
  greeting: { color: '#fff', fontSize: 22, fontWeight: '800', marginTop: 18 },
  content: { padding: 20, paddingBottom: 118 },
  authBg: { flex: 1, backgroundColor: '#020713' },
  authBgImage: { opacity: 0.28 },
  authOverlay: { flex: 1, padding: 24, justifyContent: 'center', backgroundColor: 'rgba(2,7,19,0.72)' },
  authLogo: { width: 270, height: 110, alignSelf: 'center' },
  authTitle: { color: '#fff', fontSize: 30, fontWeight: '900', textAlign: 'center', marginBottom: 24 },
  authCard: { backgroundColor: 'rgba(12,20,34,0.9)', borderColor: '#1d2b43', borderWidth: 1, borderRadius: 28, padding: 20 },
  input: { minHeight: 52, backgroundColor: '#07111f', borderColor: '#1b2a40', borderWidth: 1, borderRadius: 14, paddingHorizontal: 16, color: '#fff', marginBottom: 12, fontWeight: '700' },
  primaryButton: { minHeight: 54, borderRadius: 14, backgroundColor: '#ff6b00', alignItems: 'center', justifyContent: 'center', marginTop: 8, shadowColor: '#ff6b00', shadowOpacity: 0.28, shadowRadius: 20 },
  primaryButtonText: { color: '#fff', fontWeight: '900', fontSize: 16 },
  disabled: { opacity: 0.6 },
  link: { color: '#ff8a2a', textAlign: 'center', fontWeight: '800', marginTop: 18 },
  searchHero: { padding: 18, borderRadius: 24, backgroundColor: '#081321', borderColor: '#17263a', borderWidth: 1 },
  heroText: { color: '#fff', fontSize: 25, fontWeight: '900', marginBottom: 14 },
  section: { marginTop: 22 },
  sectionHeader: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: 12 },
  sectionTitle: { color: '#fff', fontSize: 18, fontWeight: '900' },
  screenTitle: { color: '#fff', fontSize: 24, fontWeight: '900', marginBottom: 14 },
  actionText: { color: '#2f8cff', fontWeight: '800' },
  card: { backgroundColor: '#081321', borderColor: '#17263a', borderWidth: 1, borderRadius: 18, padding: 16, marginBottom: 12 },
  cardTitle: { color: '#fff', fontWeight: '900', fontSize: 15 },
  muted: { color: '#aeb8c8', lineHeight: 21, marginTop: 3 },
  travelRow: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#081321', borderColor: '#17263a', borderWidth: 1, borderRadius: 16, padding: 12, marginBottom: 10 },
  pin: { width: 34, height: 34, borderRadius: 17, backgroundColor: '#0f53c9', alignItems: 'center', justifyContent: 'center', marginRight: 12 },
  pinText: { color: '#fff', fontWeight: '900' },
  travelInfo: { flex: 1 },
  badge: { color: '#ff8a2a', fontWeight: '900', borderColor: '#823a06', borderWidth: 1, borderRadius: 10, paddingHorizontal: 9, paddingVertical: 5 },
  quickGrid: { flexDirection: 'row', flexWrap: 'wrap', gap: 10 },
  quick: { width: '48%', backgroundColor: '#081321', borderColor: '#17263a', borderWidth: 1, borderRadius: 18, padding: 16, alignItems: 'center' },
  quickIcon: { color: '#ff7a00', fontSize: 24, fontWeight: '900' },
  quickText: { color: '#fff', fontWeight: '800', marginTop: 8 },
  noticeRow: { flexDirection: 'row', gap: 12, marginTop: 22 },
  infoPill: { flex: 1, backgroundColor: '#081321', borderColor: '#17263a', borderWidth: 1, borderRadius: 18, padding: 14 },
  infoTitle: { color: '#fff', fontWeight: '900' },
  scheduleCard: { flexDirection: 'row', justifyContent: 'space-between', backgroundColor: '#081321', borderColor: '#17263a', borderWidth: 1, borderRadius: 16, padding: 16, marginBottom: 10 },
  selectedCard: { borderColor: '#ff6b00', backgroundColor: '#351607' },
  scheduleTime: { color: '#fff', fontSize: 20, fontWeight: '900' },
  price: { color: '#fff', fontSize: 18, fontWeight: '900' },
  seatGrid: { flexDirection: 'row', flexWrap: 'wrap', gap: 9 },
  seat: { width: 48, height: 42, borderRadius: 10, borderWidth: 1, borderColor: '#3a4657', alignItems: 'center', justifyContent: 'center', backgroundColor: '#0c1624' },
  seatOccupied: { backgroundColor: '#1f2937', borderColor: '#334155', opacity: 0.45 },
  seatSelected: { backgroundColor: '#ff6b00', borderColor: '#ff9a33' },
  seatText: { color: '#fff', fontWeight: '900' },
  ticket: { backgroundColor: '#081321', borderColor: '#17263a', borderWidth: 1, borderRadius: 24, padding: 18, marginBottom: 14 },
  ticketTop: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  ticketCode: { color: '#fff', fontSize: 24, fontWeight: '900' },
  busBadge: { color: '#fff', backgroundColor: '#ff6b00', overflow: 'hidden', borderRadius: 18, padding: 10 },
  status: { color: '#27d985', fontWeight: '900', marginTop: 4, marginBottom: 16 },
  ticketGrid: { flexDirection: 'row', flexWrap: 'wrap' },
  pair: { width: '50%', marginBottom: 14 },
  label: { color: '#8792a4', fontWeight: '700' },
  value: { color: '#fff', fontWeight: '900', marginTop: 4 },
  qrMock: { backgroundColor: '#fff', borderRadius: 14, minHeight: 150, alignItems: 'center', justifyContent: 'center', marginTop: 4 },
  qrText: { color: '#111827', fontSize: 48, fontWeight: '900' },
  qrHint: { color: '#334155', marginTop: 8 },
  qrPending: { backgroundColor: '#0c1624', borderColor: '#334155', borderWidth: 1, borderRadius: 14, minHeight: 120, alignItems: 'center', justifyContent: 'center', marginTop: 4, padding: 18 },
  pendingTitle: { color: '#ff8a2a', fontSize: 18, fontWeight: '900', marginBottom: 8 },
  bottomNav: { position: 'absolute', bottom: 0, left: 0, right: 0, minHeight: 84, flexDirection: 'row', backgroundColor: '#050b14', borderTopColor: '#162234', borderTopWidth: 1, paddingTop: 8, paddingHorizontal: 4 },
  navItem: { flex: 1, alignItems: 'center' },
  navIcon: { color: '#8b96a8', fontSize: 20, fontWeight: '900' },
  navLabel: { color: '#8b96a8', fontSize: 10, fontWeight: '800', marginTop: 3 },
  navActive: { color: '#ff7a00' },
});
